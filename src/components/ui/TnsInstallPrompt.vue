<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const deferredPrompt = ref<any>(null)
const dismissed      = ref(false)

// Détection iOS
const isIos = computed(() =>
  /iphone|ipad|ipod/i.test(navigator.userAgent) && !(window as any).MSStream
)
const isInStandaloneMode = computed(() =>
  ('standalone' in window.navigator) && (window.navigator as any).standalone
)

const show = computed(() =>
  !dismissed.value && !isInStandaloneMode.value &&
  (deferredPrompt.value !== null || isIos.value)
)

function onBeforeInstallPrompt(e: Event) {
  e.preventDefault()
  deferredPrompt.value = e
}

async function install() {
  if (!deferredPrompt.value) return
  deferredPrompt.value.prompt()
  const { outcome } = await deferredPrompt.value.userChoice
  if (outcome === 'accepted') dismissed.value = true
  deferredPrompt.value = null
}

const { t } = useI18n()

onMounted(() => window.addEventListener('beforeinstallprompt', onBeforeInstallPrompt))
onUnmounted(() => window.removeEventListener('beforeinstallprompt', onBeforeInstallPrompt))
</script>

<template>
  <Transition name="slide-up">
    <div v-if="show" class="install-banner">
      <div class="install-icon">📲</div>

      <div class="install-body">
        <p class="install-title">{{ t('pwa.install.title') }}</p>

        <!-- Android / Chrome -->
        <p v-if="deferredPrompt" class="install-sub">{{ t('pwa.install.description') }}</p>

        <!-- iOS -->
        <p v-else-if="isIos" class="install-sub">{{ t('pwa.install.ios') }}</p>
      </div>

      <div class="install-actions">
        <button class="btn-dismiss" @click="dismissed = true">✕</button>
        <button v-if="deferredPrompt" class="btn-install" @click="install">{{ t('pwa.install.install') }}</button>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.install-banner {
  position: fixed;
  bottom: calc(env(safe-area-inset-bottom) + 80px);
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% - 32px);
  max-width: 480px;
  background: var(--tns-card);
  border: 0.5px solid var(--tns-sep);
  border-radius: var(--tns-radius-lg);
  box-shadow: 0 8px 24px rgb(0 0 0 / 0.18);
  padding: 14px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  z-index: 9998;
  font-family: var(--tns-font);
}

.install-icon {
  font-size: 24px;
  flex-shrink: 0;
}

.install-body {
  flex: 1;
  min-width: 0;
}

.install-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--tns-fg);
  margin: 0 0 2px;
}

.install-sub {
  font-size: 12px;
  color: var(--tns-fg2);
  margin: 0;
  line-height: 1.4;
}

.install-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.btn-dismiss {
  background: none;
  border: none;
  font-size: 14px;
  color: var(--tns-fg3);
  cursor: pointer;
  padding: 4px;
  line-height: 1;
}

.btn-install {
  background: var(--tns-accent);
  border: none;
  border-radius: var(--tns-radius-md);
  font-size: 14px;
  font-weight: 600;
  font-family: var(--tns-font);
  color: #fff;
  cursor: pointer;
  padding: 6px 14px;
  white-space: nowrap;
}

.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.25s ease, opacity 0.25s ease;
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateX(-50%) translateY(20px);
  opacity: 0;
}
</style>
