<script setup lang="ts">
import { computed, ref } from 'vue'
import { IonContent, IonIcon, IonPage, IonSpinner } from '@ionic/vue'
import {
  syncOutline, phonePortraitOutline,
  checkmarkCircleOutline, alertCircleOutline,
} from 'ionicons/icons'
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
} = useSync()

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
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" :style="{ '--background': 'var(--tns-bg)' }">
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
    </ion-content>
  </ion-page>
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
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

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
</style>
