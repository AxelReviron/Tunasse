<template>
  <!-- TnsFilterChips — horizontal scroll row of pills.
       v-model holds the active chip's value. -->
  <div class="tns-chips">
    <button
      v-for="c in chips" :key="c.value"
      class="tns-chip" :class="{ active: modelValue === c.value }"
      @click="$emit('update:modelValue', c.value)">
      {{ c.label }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [String, Number], required: true },
  /** Array of { value, label } — e.g. [{value:'all',label:'All'}, …]. */
  chips:      { type: Array, required: true },
});
defineEmits(['update:modelValue']);
</script>

<style scoped>
.tns-chips {
  display: flex; gap: 6px; padding: 0 16px 10px;
  overflow-x: auto; scrollbar-width: none;
  font-family: var(--tns-font);
}
.tns-chips::-webkit-scrollbar { display: none; }

.tns-chip {
  flex-shrink: 0;
  padding: 7px 12px;
  border-radius: 8px;
  background: rgba(120, 120, 128, 0.14);
  font-size: 13px; font-weight: 500;
  color: var(--tns-fg);
  border: none; cursor: pointer; white-space: nowrap;
  font-family: inherit;
}
.tns-chip.active { background: var(--tns-accent); color: #fff; }
</style>
