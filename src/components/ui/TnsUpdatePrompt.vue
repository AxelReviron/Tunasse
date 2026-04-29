<script setup lang="ts">
import { useRegisterSW } from 'virtual:pwa-register/vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const { needRefresh, updateServiceWorker } = useRegisterSW()
</script>

<template>
  <Transition name="slide-up">
    <div v-if="needRefresh" class="update-banner">
      <span class="update-text">{{ t('pwa.update.message') }}</span>
      <div class="update-actions">
        <button class="btn-later" @click="needRefresh = false">{{ t('pwa.update.later') }}</button>
        <button class="btn-update" @click="updateServiceWorker()">{{ t('pwa.update.refresh') }}</button>
      </div>

    </div>
  </Transition>
</template>

<style scoped>
.update-banner {
  position: fixed;
  bottom: calc(env(safe-area-inset-bottom) + 72px);
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
  justify-content: space-between;
  gap: 12px;
  z-index: 9999;
  font-family: var(--tns-font);
}

.update-text {
  font-size: 14px;
  color: var(--tns-fg);
  flex: 1;
  min-width: 0;
}

.update-actions {
  display: flex;
  gap: 8px;
  flex-shrink: 0;
}

.btn-later {
  background: none;
  border: none;
  font-size: 14px;
  font-family: var(--tns-font);
  color: var(--tns-fg2);
  cursor: pointer;
  padding: 6px 8px;
}

.btn-update {
  background: var(--tns-accent);
  border: none;
  border-radius: var(--tns-radius-md);
  font-size: 14px;
  font-weight: 600;
  font-family: var(--tns-font);
  color: #fff;
  cursor: pointer;
  padding: 6px 14px;
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
