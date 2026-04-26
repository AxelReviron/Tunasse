<script setup lang="ts">
import { PRESET_COLORS } from '@/constants/colors'

defineProps<{ modelValue: string }>()
defineEmits<{ 'update:modelValue': [color: string] }>()
</script>

<template>
  <div class="tns-color-picker">
    <button
      v-for="color in PRESET_COLORS"
      :key="color"
      class="tns-color-swatch"
      :class="{ active: modelValue === color }"
      :style="{ background: color }"
      @click="$emit('update:modelValue', color)"
    />
  </div>
</template>

<style scoped>
.tns-color-picker {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  padding: 4px 0;
}
.tns-color-swatch {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 2.5px solid transparent;
  cursor: pointer;
  transition: transform .15s, border-color .15s;
  flex-shrink: 0;
}
.tns-color-swatch.active {
  border-color: var(--tns-fg);
  transform: scale(1.15);
}
</style>
