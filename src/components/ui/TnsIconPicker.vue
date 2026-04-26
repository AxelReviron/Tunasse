<script setup lang="ts">
import { IonIcon } from '@ionic/vue'
import { ICON_MAP, ICON_LIST } from '@/constants/icons'

defineProps<{ modelValue: string }>()
defineEmits<{ 'update:modelValue': [icon: string] }>()
</script>

<template>
  <div class="tns-icon-picker">
    <button
      v-for="name in ICON_LIST"
      :key="name"
      class="tns-icon-btn"
      :class="{ active: modelValue === name }"
      @click="$emit('update:modelValue', name)"
    >
      <ion-icon :icon="ICON_MAP[name]" />
    </button>
  </div>
</template>

<style scoped>
.tns-icon-picker {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  padding: 4px 0;
}
.tns-icon-btn {
  font-size: 22px;
  width: 40px;
  height: 40px;
  border-radius: var(--tns-radius-md);
  border: none;
  background: var(--tns-bg);
  color: var(--tns-fg2);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background .15s, color .15s;
}
.tns-icon-btn.active {
  background: var(--tns-accent);
  color: #fff;
}
</style>
