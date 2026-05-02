<script setup>
import {IonIcon} from "@ionic/vue";
import { helpCircleOutline } from 'ionicons/icons';
defineProps({
  modelValue: { type: Boolean, required: true },
  title:      { type: String,  required: true },
  body:        { type: String,  required: true },
})
defineEmits(['update:modelValue'])
</script>

<template>
  <Teleport to=".tns-main">
    <Transition name="tns-info">
      <div v-if="modelValue" class="tns-info-backdrop" @click.self="$emit('update:modelValue', false)">
        <div class="tns-info-modal" role="dialog" :aria-label="title">
          <div class="tns-info-header">
            <div class="tns-info-header-title-icon">
              <ion-icon :icon="helpCircleOutline"/>
              <span class="tns-info-title">{{ title }}</span>
            </div>
            <button class="tns-info-close" @click="$emit('update:modelValue', false)">✕</button>
          </div>
          <div class="tns-info-sep" />
          <p class="tns-info-body">{{ body }}</p>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.tns-info-backdrop {
  position: absolute; inset: 0;
  background: rgba(0, 0, 0, 0.45);
  z-index: 40;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
}

.tns-info-modal {
  width: 100%;
  max-width: 360px;
  height: 240px;
  background: var(--tns-card);
  border-radius: var(--tns-radius-xl);
  padding: 20px 20px 0;
  font-family: var(--tns-font);
  display: flex;
  flex-direction: column;
}

.tns-info-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  flex-shrink: 0;
}
.tns-info-header-title-icon {
  display: flex;
  justify-items: center;
  align-items: center;
  gap: 4px;
}
.tns-info-header-title-icon > ion-icon {
  font-size: 20px;
}
.tns-info-title {
  font-size: 17px;
  font-weight: 700;
  color: var(--tns-fg);
  letter-spacing: -0.2px;
  line-height: 1.3;
}
.tns-info-close {
  background: none;
  border: none;
  font-size: 16px;
  color: var(--tns-fg3);
  cursor: pointer;
  flex-shrink: 0;
  padding: 0;
  line-height: 1;
}

.tns-info-sep {
  height: 0.5px;
  background: var(--tns-sep);
  margin: 14px 0;
  flex-shrink: 0;
}

.tns-info-body {
  font-size: 14px;
  line-height: 1.6;
  color: var(--tns-fg2);
  margin: 0;
  overflow-y: auto;
  padding-bottom: 20px;
}

/* ── Transition ───────────────────────────────────────────────────────────── */
.tns-info-enter-active,
.tns-info-leave-active { transition: opacity .18s ease; }
.tns-info-enter-from,
.tns-info-leave-to    { opacity: 0; }

.tns-info-enter-active .tns-info-modal,
.tns-info-leave-active .tns-info-modal { transition: transform .2s ease; }
.tns-info-enter-from   .tns-info-modal,
.tns-info-leave-to     .tns-info-modal { transform: scale(0.95); }
</style>
