import { ref, onUnmounted } from 'vue'
import { joinRoom } from 'trystero'
import { SyncService, type SyncDump, type MergeResult } from '@/services/SyncService'
import { BIP39_WORDLIST } from '@/constants/wordlist'

const PASSPHRASE_KEY    = 'tunasse_passphrase'
const DEVICE_NAME_KEY   = 'tunasse_device_name'
const KNOWN_DEVICES_KEY = 'tunasse_known_devices'
const TURN_CONFIG_KEY   = 'tunasse_turn_config'
const MAX_KNOWN_DEVICES = 10

export type KnownDevice = { passphrase: string; name: string; lastSeen: string }
export type PeerInfo    = { name: string; passphrase: string }
export type TurnConfig  = { host: string; port: number; username: string; credential: string }

type SyncMessage  = { kind: 'push' | 'pull'; dump: SyncDump }
type HelloMessage = { name: string; passphrase: string }
type SendFn<T>    = (data: T, target?: string) => void

// ── Helpers localStorage ──────────────────────────────────────────────────────

function generatePassphrase(): string {
  const arr = new Uint32Array(4)
  crypto.getRandomValues(arr)
  return Array.from(arr).map(n => BIP39_WORDLIST[n % BIP39_WORDLIST.length]).join('-')
}

function getOrCreatePassphrase(): string {
  let p = localStorage.getItem(PASSPHRASE_KEY)
  if (!p) { p = generatePassphrase(); localStorage.setItem(PASSPHRASE_KEY, p) }
  return p
}

function defaultDeviceName(passphrase: string): string {
  return passphrase.split('-').slice(0, 2).join('-')
}

function loadKnownDevices(): KnownDevice[] {
  try { return JSON.parse(localStorage.getItem(KNOWN_DEVICES_KEY) ?? '[]') } catch { return [] }
}

function persistKnownDevices(devices: KnownDevice[]) {
  localStorage.setItem(KNOWN_DEVICES_KEY, JSON.stringify(devices))
}

function loadTurnConfig(): TurnConfig | null {
  try { return JSON.parse(localStorage.getItem(TURN_CONFIG_KEY) ?? 'null') } catch { return null }
}

function buildIceServers(cfg: TurnConfig | null): RTCIceServer[] {
  if (!cfg?.host || !cfg.username || !cfg.credential) return []
  return [
    { urls: `stun:${cfg.host}:${cfg.port || 3478}` },
    { urls: `turn:${cfg.host}:${cfg.port || 3478}`, username: cfg.username, credential: cfg.credential },
  ]
}

// ── Composable ────────────────────────────────────────────────────────────────

export function useSync() {
  const ownPassphrase    = ref(getOrCreatePassphrase())
  const activePassphrase = ref(ownPassphrase.value)
  const deviceName       = ref(localStorage.getItem(DEVICE_NAME_KEY) ?? '')
  const peers            = ref<string[]>([])
  const connectedPeers   = ref<Record<string, PeerInfo>>({})
  const isSyncing        = ref(false)
  const syncError        = ref<string | null>(null)
  const syncSuccess      = ref(false)
  const knownDevices     = ref<KnownDevice[]>(loadKnownDevices())
  const turnConfig  = ref<TurnConfig | null>(loadTurnConfig())
  const turnStatus  = ref<'idle' | 'testing' | 'ok' | 'fail'>('idle')

  async function checkTurn() {
    const cfg = turnConfig.value
    if (!cfg) { turnStatus.value = 'idle'; return }
    turnStatus.value = 'testing'
    const ok = await new Promise<boolean>(resolve => {
      const pc = new RTCPeerConnection({ iceServers: buildIceServers(cfg) })
      let done = false
      const finish = (result: boolean) => {
        if (done) return
        done = true
        clearTimeout(timer)
        pc.close()
        resolve(result)
      }
      const timer = setTimeout(() => finish(false), 5000)
      pc.addEventListener('icecandidate', e => {
        if (e.candidate?.type === 'relay') finish(true)
        else if (e.candidate === null) finish(false)
      })
      pc.createDataChannel('_t')
      pc.createOffer().then(o => pc.setLocalDescription(o)).catch(() => finish(false))
    })
    turnStatus.value = ok ? 'ok' : 'fail'
  }

  const canSync = () =>
    deviceName.value.trim() !== '' && peers.value.length > 0 && !isSyncing.value

  let pendingAcks  = new Set<string>()
  let _sendMessage: SendFn<SyncMessage>  = () => {}
  let _sendAck:     SendFn<MergeResult>  = () => {}
  let _sendHello:   SendFn<HelloMessage> = () => {}
  let _currentRoom: ReturnType<typeof joinRoom> | null = null

  function setupRoom(passphrase: string) {
    if (_currentRoom) {
      _currentRoom.leave()
      peers.value          = []
      connectedPeers.value = {}
      pendingAcks          = new Set()
      isSyncing.value      = false
    }

    const room = joinRoom(
      {
        appId: 'tunasse',
        rtcConfig: { iceServers: buildIceServers(turnConfig.value) },
      },
      passphrase,
    )
    const [sendMsg,   receiveMsg]   = room.makeAction<SyncMessage>('sync-data')
    const [sendAck,   receiveAck]   = room.makeAction<MergeResult>('sync-ack')
    const [sendHello, receiveHello] = room.makeAction<HelloMessage>('device-hello')

    _sendMessage = sendMsg   as SendFn<SyncMessage>
    _sendAck     = sendAck   as SendFn<MergeResult>
    _sendHello   = sendHello as SendFn<HelloMessage>

    // ── Hello ──
    receiveHello((msg, peerId) => {
      connectedPeers.value = { ...connectedPeers.value, [peerId]: msg }

      // Ajoute ou met à jour le device dans known devices (dans les deux sens)
      const existing = knownDevices.value.find(d => d.passphrase === msg.passphrase)
      if (existing) {
        knownDevices.value = knownDevices.value.map(d =>
          d.passphrase === msg.passphrase
            ? { ...d, name: msg.name, lastSeen: new Date().toISOString() }
            : d
        )
      } else {
        knownDevices.value = [
          { passphrase: msg.passphrase, name: msg.name, lastSeen: new Date().toISOString() },
          ...knownDevices.value,
        ].slice(0, MAX_KNOWN_DEVICES)
      }
      persistKnownDevices(knownDevices.value)
    })

    room.onPeerJoin(peerId => {
      peers.value = [...peers.value, peerId]
      if (deviceName.value.trim()) {
        _sendHello({ name: deviceName.value, passphrase: ownPassphrase.value }, peerId)
      }
    })

    room.onPeerLeave(peerId => {
      peers.value = peers.value.filter(id => id !== peerId)
      const updated = { ...connectedPeers.value }
      delete updated[peerId]
      connectedPeers.value = updated
      pendingAcks.delete(peerId)
      if (pendingAcks.size === 0 && isSyncing.value) isSyncing.value = false
    })

    // ── Sync data ──
    receiveMsg(async (msg, peerId) => {
      const result = await SyncService.mergeDump(msg.dump)
      _sendAck(result, peerId)
      if (result.ok && msg.kind === 'push') {
        const localDump = await SyncService.exportDump()
        _sendMessage({ kind: 'pull', dump: localDump }, peerId)
      }
    })

    receiveAck(async ({ ok, error }, peerId) => {
      if (!ok) syncError.value = error ?? 'sync_failed'
      pendingAcks.delete(peerId)
      if (pendingAcks.size === 0) {
        isSyncing.value = false
        if (!syncError.value) {
          await SyncService.clearDeletions()
          syncSuccess.value = true
          setTimeout(() => { syncSuccess.value = false }, 3000)
        }
      }
    })

    _currentRoom = room
  }

  setupRoom(ownPassphrase.value)

  // ── Actions publiques ─────────────────────────────────────────────────────

  function setDeviceName(name: string) {
    deviceName.value = name
    localStorage.setItem(DEVICE_NAME_KEY, name)
    if (peers.value.length > 0 && name.trim()) {
      _sendHello({ name, passphrase: ownPassphrase.value })
    }
  }

  function joinRemote(remotePassphrase: string) {
    const p = remotePassphrase.trim().toLowerCase()
    if (p === ownPassphrase.value) return
    activePassphrase.value = p

    const existing = knownDevices.value.find(d => d.passphrase === p)
    if (existing) {
      knownDevices.value = knownDevices.value.map(d =>
        d.passphrase === p ? { ...d, lastSeen: new Date().toISOString() } : d
      )
    } else {
      knownDevices.value = [
        { passphrase: p, name: defaultDeviceName(p), lastSeen: new Date().toISOString() },
        ...knownDevices.value,
      ].slice(0, MAX_KNOWN_DEVICES)
    }
    persistKnownDevices(knownDevices.value)
    setupRoom(p)
  }

  function saveTurnConfig(cfg: TurnConfig | null) {
    turnConfig.value = cfg
    if (cfg) localStorage.setItem(TURN_CONFIG_KEY, JSON.stringify(cfg))
    else localStorage.removeItem(TURN_CONFIG_KEY)
    setupRoom(activePassphrase.value)
    checkTurn()
  }

  function disconnect() {
    activePassphrase.value = ownPassphrase.value
    setupRoom(ownPassphrase.value)
  }

  function renameDevice(passphrase: string, name: string) {
    knownDevices.value = knownDevices.value.map(d =>
      d.passphrase === passphrase ? { ...d, name: name.trim() || defaultDeviceName(passphrase) } : d
    )
    persistKnownDevices(knownDevices.value)
  }

  function forgetDevice(passphrase: string) {
    knownDevices.value = knownDevices.value.filter(d => d.passphrase !== passphrase)
    persistKnownDevices(knownDevices.value)
    if (activePassphrase.value === passphrase) disconnect()
  }

  async function sync(targetPeerId?: string) {
    if (!canSync()) return
    isSyncing.value   = true
    syncError.value   = null
    syncSuccess.value = false

    const targets = targetPeerId ? [targetPeerId] : [...peers.value]
    if (!targets.length) { isSyncing.value = false; return }

    pendingAcks = new Set(targets)
    const dump  = await SyncService.exportDump()
    for (const target of targets) _sendMessage({ kind: 'push', dump }, target)
  }

  onUnmounted(() => _currentRoom?.leave())

  return {
    ownPassphrase, activePassphrase, deviceName,
    peers, connectedPeers, isSyncing, syncError, syncSuccess,
    knownDevices, canSync,
    sync, setDeviceName, joinRemote, disconnect, renameDevice, forgetDevice,
    turnConfig, turnStatus, saveTurnConfig, checkTurn,
  }
}
