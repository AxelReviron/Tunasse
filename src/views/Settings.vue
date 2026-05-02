<script setup lang="ts">
import { computed, ref, onMounted } from 'vue'
import { IonIcon, IonSpinner } from '@ionic/vue'
import TnsPage from '@/components/ui/TnsPage.vue'
import {
  syncOutline, phonePortraitOutline,
  checkmarkCircleOutline, alertCircleOutline,
  constructOutline, chevronDownOutline, chevronForwardOutline,
} from 'ionicons/icons'
import type { TurnConfig } from '@/composables/useSync'
import { useI18n } from 'vue-i18n'
import TnsLargeTitle from '@/components/ui/TnsLargeTitle.vue'
import TnsList from '@/components/ui/TnsList.vue'
import { useSync } from '@/composables/useSync'

const { t } = useI18n()
const {
  ownPassphrase, deviceName,
  peers, connectedPeers, isSyncing, syncError, syncSuccess,
  canSync,
  sync, setDeviceName, joinRemote,
  turnConfig, turnStatus, saveTurnConfig, checkTurn,
} = useSync()

onMounted(() => { if (turnConfig.value) checkTurn() })

const hasPeers          = computed(() => peers.value.length > 0)
const deviceNameMissing = computed(() => hasPeers.value && deviceName.value.trim() === '')
const peerList          = computed(() => Object.entries(connectedPeers.value))

// ── Device name ───────────────────────────────────────────────────────────────
const localDeviceName = ref(deviceName.value)

function saveDeviceName() {
  setDeviceName(localDeviceName.value.trim())
}

// ── Passphrase copy ───────────────────────────────────────────────────────────
const copied = ref(false)

function copyPassphrase() {
  navigator.clipboard.writeText(ownPassphrase.value)
  copied.value = true
  setTimeout(() => { copied.value = false }, 2000)
}

// ── Join remote ───────────────────────────────────────────────────────────────
const remoteInput = ref('')

function handleConnect() {
  const val = remoteInput.value.trim()
  if (!val) return
  joinRemote(val)
  remoteInput.value = ''
}

// ── Advanced collapse ─────────────────────────────────────────────────────────
const showAdvanced = ref(false)

// ── TURN config ───────────────────────────────────────────────────────────────
const turnHost       = ref(turnConfig.value?.host ?? '')
const turnPort       = ref<number>(turnConfig.value?.port ?? 3478)
const turnUsername   = ref(turnConfig.value?.username ?? '')
const turnCredential = ref(turnConfig.value?.credential ?? '')
const turnSaved      = ref(false)

function saveTurn() {
  const cfg: TurnConfig = {
    host:       turnHost.value.trim(),
    port:       turnPort.value || 3478,
    username:   turnUsername.value.trim(),
    credential: turnCredential.value,
  }
  saveTurnConfig(cfg)
  turnSaved.value = true
  setTimeout(() => { turnSaved.value = false }, 2000)
}

function resetTurn() {
  saveTurnConfig(null)
  turnHost.value       = ''
  turnPort.value       = 3478
  turnUsername.value   = ''
  turnCredential.value = ''
}
</script>

<template>
  <TnsPage>
    <div class="tns-page">
        <TnsLargeTitle :title="t('settings.title')" />

        <!-- ── En-tête section ──────────────────────────────────── -->
        <div class="tns-list-hdr">
          <div class="tns-section-header-row">
            <ion-icon :icon="syncOutline" />
            <span class="tns-list-hdr-title">{{ t('settings.sync.title') }}</span>
          </div>
        </div>
        <p class="section-desc">{{ t('settings.sync.description') }}</p>

        <TnsList>

          <!-- Statut -->
          <div class="srow srow-status">
            <span class="status-dot" :class="hasPeers ? 'connected' : 'searching'" />
            <span class="srow-text">
              {{ hasPeers
                ? t('settings.sync.connected', peers.length)
                : t('settings.sync.searching') }}
            </span>
          </div>

          <!-- Mon appareil -->
          <div class="srow srow-block">
            <p class="block-label">{{ t('settings.sync.myDevice') }}</p>
            <input
              v-model="localDeviceName"
              class="device-input"
              :class="{ 'device-input--required': deviceNameMissing }"
              :placeholder="t('settings.sync.myDevicePlaceholder')"
              @blur="saveDeviceName"
              @keydown.enter="saveDeviceName"
            />
          </div>

          <!-- Mon code -->
          <div class="srow srow-block">
            <p class="block-label">{{ t('settings.sync.myCode') }}</p>
            <div class="inline-row">
              <span class="mono-box">{{ ownPassphrase }}</span>
              <button class="btn-action" @click="copyPassphrase">
                {{ copied ? t('settings.sync.copied') : t('settings.sync.copy') }}
              </button>
            </div>
          </div>

          <!-- Autre appareil -->
          <div class="srow srow-block">
            <p class="block-label">{{ t('settings.sync.otherDevice') }}</p>
            <div class="inline-row">
              <input
                v-model="remoteInput"
                class="mono-input"
                :placeholder="t('settings.sync.codePlaceholder')"
                autocomplete="off"
                autocorrect="off"
                autocapitalize="none"
                spellcheck="false"
                @keydown.enter="handleConnect"
              />
              <button class="btn-action" :disabled="!remoteInput.trim()" @click="handleConnect">
                {{ t('settings.sync.connect') }}
              </button>
            </div>
          </div>

          <!-- Appareils connectés -->
          <div v-if="hasPeers" class="srow srow-block">
            <p class="block-label">{{ t('settings.sync.peersSection') }}</p>
            <div v-for="[peerId, info] in peerList" :key="peerId" class="peer-row">
              <div class="device-ico">
                <ion-icon :icon="phonePortraitOutline" />
              </div>
              <span class="peer-name">{{ info.name || '…' }}</span>
              <span class="peer-dot" />
            </div>
          </div>

          <!-- Avancé (collapsible) -->
          <div class="srow srow-adv-toggle" @click="showAdvanced = !showAdvanced">
            <ion-icon :icon="constructOutline" class="adv-toggle-icon" />
            <span class="srow-text adv-toggle-label">{{ t('settings.advanced.title') }}</span>
            <ion-icon :icon="showAdvanced ? chevronDownOutline : chevronForwardOutline" class="adv-chevron" />
          </div>

          <template v-if="showAdvanced">
            <div class="srow srow-block">
              <p class="block-label">{{ t('settings.advanced.turn.title') }}</p>
              <p class="turn-desc">{{ t('settings.advanced.turn.description') }}</p>
              <a
                class="turn-link"
                href="https://github.com/AxelReviron/Tunasse#self-hosting-a-turn-server"
                target="_blank"
                rel="noopener"
              >
                {{ t('settings.advanced.turn.link') }}
              </a>
              <div class="turn-status">
                <span class="turn-status-dot" :class="turnConfig ? turnStatus : 'idle'" />
                <span class="turn-status-text">
                  {{ turnConfig ? `${turnConfig.host}:${turnConfig.port || 3478}` : t('settings.advanced.turn.noServer') }}
                </span>
                <button v-if="turnConfig" class="btn-retest" :disabled="turnStatus === 'testing'" @click.stop="checkTurn">
                  <ion-spinner v-if="turnStatus === 'testing'" name="crescent" class="retest-spinner" />
                  <span v-else>↻</span>
                </button>
              </div>
            </div>

            <div class="srow srow-block">
              <p class="block-label">{{ t('settings.advanced.turn.host') }}</p>
              <input
                v-model="turnHost"
                class="device-input"
                placeholder="192.168.1.100"
                autocomplete="off"
                autocorrect="off"
                autocapitalize="none"
                spellcheck="false"
              />
            </div>

            <div class="srow srow-block">
              <p class="block-label">{{ t('settings.advanced.turn.port') }}</p>
              <input
                v-model.number="turnPort"
                class="device-input"
                type="number"
                placeholder="3478"
              />
            </div>

            <div class="srow srow-block">
              <p class="block-label">{{ t('settings.advanced.turn.username') }}</p>
              <input
                v-model="turnUsername"
                class="device-input"
                autocomplete="off"
                autocorrect="off"
                autocapitalize="none"
                spellcheck="false"
              />
            </div>

            <div class="srow srow-block">
              <p class="block-label">{{ t('settings.advanced.turn.password') }}</p>
              <input
                v-model="turnCredential"
                class="device-input"
                type="password"
                autocomplete="new-password"
              />
            </div>

            <div class="srow srow-turn-actions">
              <button class="btn-reset" :disabled="!turnConfig" @click="resetTurn">
                {{ t('settings.advanced.turn.reset') }}
              </button>
              <button
                class="btn-action"
                :disabled="!turnHost.trim() || !turnUsername.trim() || !turnCredential"
                @click="saveTurn"
              >
                {{ turnSaved ? t('settings.advanced.turn.saved') : t('settings.advanced.turn.save') }}
              </button>
            </div>
          </template>

          <!-- Bouton sync -->
          <div class="srow srow-sync-wrap">
            <p v-if="deviceNameMissing" class="sync-required">
              {{ t('settings.sync.myDeviceRequired') }}
            </p>
            <button
              class="sync-btn"
              :disabled="!canSync()"
              @click="sync()"
            >
              <ion-spinner v-if="isSyncing" name="crescent" class="btn-spinner" />
              <ion-icon v-else :icon="syncOutline" />
              {{ isSyncing ? t('settings.sync.syncing') : t('settings.sync.syncButton') }}
            </button>
          </div>

        </TnsList>

        <!-- Feedback -->
        <div v-if="syncSuccess" class="feedback success">
          <ion-icon :icon="checkmarkCircleOutline" />
          {{ t('settings.sync.success') }}
        </div>
        <div v-if="syncError" class="feedback error">
          <ion-icon :icon="alertCircleOutline" />
          {{ t('settings.sync.error', { msg: syncError }) }}
        </div>

      </div>
  </TnsPage>
</template>

<style scoped>
/* ── Section header ───────────────────────────────────── */
.tns-list-hdr {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  padding: 0 16px;
  margin-bottom: 6px;
}
.tns-section-header-row {
  display: flex;
  align-items: center;
  gap: 6px;
  color: var(--tns-fg2);
}
.tns-list-hdr-title {
  font-family: var(--tns-font);
  font-size: 18px;
  font-weight: 600;
  color: var(--tns-fg);
  letter-spacing: -0.2px;
}

.section-desc {
  font-family: var(--tns-font);
  font-size: 13px;
  color: var(--tns-fg2);
  line-height: 1.5;
  margin: 0 16px 12px;
}

/* ── Rows ─────────────────────────────────────────────── */
.srow {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  background: var(--tns-card);
  font-family: var(--tns-font);
}
.srow + .srow { border-top: 0.5px solid var(--tns-sep); }
.srow-text { font-size: 15px; color: var(--tns-fg); }

/* ── Status ───────────────────────────────────────────── */
.srow-status { gap: 10px; }
.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}
.status-dot.connected {
  background: var(--tns-green);
  box-shadow: 0 0 0 3px rgb(34 197 94 / 0.18);
}
.status-dot.searching {
  background: var(--tns-fg3);
  animation: blink 1.8s ease-in-out infinite;
}
@keyframes blink {
  0%, 100% { opacity: 1; }
  50%       { opacity: 0.25; }
}

/* ── Block rows ───────────────────────────────────────── */
.srow-block {
  flex-direction: column;
  align-items: flex-start;
  gap: 8px;
}

.block-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--tns-fg2);
  margin: 0;
}

/* ── Mon appareil ─────────────────────────────────────── */
.device-input {
  width: 100%;
  background: var(--tns-bg);
  border: none;
  border-radius: var(--tns-radius-md);
  outline: none;
  font-size: 15px;
  color: var(--tns-fg);
  font-family: var(--tns-font);
  padding: 9px 12px;
  box-sizing: border-box;
  transition: box-shadow 0.15s;
}
.device-input::placeholder { color: var(--tns-fg3); }
.device-input--required {
  box-shadow: 0 0 0 1.5px var(--tns-red);
}

/* ── Ligne [élément] [bouton] ─────────────────────────── */
.inline-row {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
}

.mono-box {
  flex: 1;
  background: var(--tns-bg);
  border-radius: var(--tns-radius-md);
  padding: 8px 10px;
  font-family: monospace;
  font-size: 14px;
  color: var(--tns-fg);
  letter-spacing: 0.03em;
  min-width: 0;
  overflow-x: auto;
  white-space: nowrap;
  scrollbar-width: none;
}
.mono-box::-webkit-scrollbar { display: none; }

.mono-input {
  flex: 1;
  background: var(--tns-bg);
  border: none;
  border-radius: var(--tns-radius-md);
  outline: none;
  font-size: 14px;
  color: var(--tns-fg);
  font-family: monospace;
  padding: 8px 10px;
  min-width: 0;
}
.mono-input::placeholder { color: var(--tns-fg3); }

.btn-action {
  background: var(--tns-accent);
  color: #fff;
  border: none;
  border-radius: var(--tns-radius-lg);
  font-size: 13px;
  font-weight: 600;
  padding: 8px 0;
  width: 100px;
  text-align: center;
  cursor: pointer;
  flex-shrink: 0;
  transition: opacity 0.15s;
}
.btn-action:disabled { opacity: 0.35; cursor: not-allowed; }

/* ── Appareils connectés ──────────────────────────────── */
.peer-row {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 2px 0;
}
.peer-row + .peer-row {
  border-top: 0.5px solid var(--tns-sep);
  padding-top: 7px;
  margin-top: 2px;
}

.device-ico {
  width: 24px;
  height: 24px;
  border-radius: 6px;
  background: var(--tns-bg);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  color: var(--tns-fg2);
  flex-shrink: 0;
}

.peer-name {
  flex: 1;
  font-size: 14px;
  color: var(--tns-fg);
  font-family: var(--tns-font);
}

.peer-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--tns-green);
  flex-shrink: 0;
}

/* ── Sync button ──────────────────────────────────────── */
.srow-sync-wrap {
  flex-direction: column;
  align-items: stretch;
  gap: 8px;
  padding: 12px 14px;
}

.sync-required {
  font-size: 12px;
  color: var(--tns-red);
  font-family: var(--tns-font);
  margin: 0;
  line-height: 1.4;
}

.sync-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 0;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 15px;
  font-weight: 600;
  color: var(--tns-accent);
  transition: opacity 0.15s;
  font-family: var(--tns-font);
}
.sync-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.btn-spinner { width: 18px; height: 18px; --color: var(--tns-accent); }

/* ── Feedback ─────────────────────────────────────────── */
.feedback {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0 16px;
  padding: 10px 14px;
  border-radius: var(--tns-radius-lg);
  font-size: 13px;
  font-family: var(--tns-font);
}
.feedback.success { background: rgb(34 197 94 / 0.1); color: var(--tns-green); }
.feedback.error   { background: rgb(244 63 94 / 0.1); color: var(--tns-red); }

/* ── Avancé (toggle) ──────────────────────────────────── */
.srow-adv-toggle {
  cursor: pointer;
  user-select: none;
}
.srow-adv-toggle:active { background: var(--tns-bg); }

.adv-toggle-icon {
  font-size: 16px;
  color: var(--tns-fg2);
  flex-shrink: 0;
}

.adv-toggle-label {
  flex: 1;
  font-size: 15px;
  color: var(--tns-fg2);
}

.adv-chevron {
  font-size: 14px;
  color: var(--tns-fg3);
  flex-shrink: 0;
  transition: transform 0.2s;
}

.turn-desc {
  font-size: 13px;
  color: var(--tns-fg2);
  line-height: 1.55;
  margin: 0;
  font-family: var(--tns-font);
}

.turn-link {
  display: inline-block;
  font-size: 13px;
  color: var(--tns-accent);
  text-decoration: none;
  margin-top: 6px;
  font-family: var(--tns-font);
}

.turn-status {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 10px;
}

.turn-status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
  transition: background 0.3s;
}
.turn-status-dot.idle    { background: var(--tns-fg3); }
.turn-status-dot.testing { background: #f59e0b; animation: blink 1s ease-in-out infinite; }
.turn-status-dot.ok      { background: var(--tns-green); }
.turn-status-dot.fail    { background: var(--tns-red); }

.btn-retest {
  background: none;
  border: none;
  padding: 0 2px;
  cursor: pointer;
  color: var(--tns-fg2);
  font-size: 15px;
  line-height: 1;
  display: flex;
  align-items: center;
}
.btn-retest:disabled { opacity: 0.4; cursor: not-allowed; }
.retest-spinner { width: 13px; height: 13px; --color: var(--tns-fg2); }

.turn-status-text {
  font-size: 13px;
  font-family: monospace;
  color: var(--tns-fg2);
}

.srow-turn-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
  padding: 12px 14px;
  background: var(--tns-card);
}

.btn-reset {
  background: none;
  border: 0.5px solid var(--tns-sep);
  border-radius: var(--tns-radius-lg);
  font-size: 13px;
  font-weight: 600;
  color: var(--tns-fg2);
  padding: 8px 14px;
  cursor: pointer;
  transition: opacity 0.15s;
  font-family: var(--tns-font);
}
.btn-reset:disabled { opacity: 0.35; cursor: not-allowed; }
</style>
