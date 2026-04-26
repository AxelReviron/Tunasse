<template>
  <div class="tns-tt">
    <button
      class="tns-tt-btn income"
      :class="{ active: modelValue === 'income' }"
      @click="$emit('update:modelValue', 'income')">
      <slot name="incomeIcon"/>
      {{ incomeLabel }}
    </button>
    <button
      class="tns-tt-btn expense"
      :class="{ active: modelValue === 'expense' }"
      @click="$emit('update:modelValue', 'expense')">
      <slot name="expenseIcon"/>
      {{ expenseLabel }}
    </button>
    <button
      class="tns-tt-btn transfer"
      :class="{ active: modelValue === 'transfer' }"
      @click="$emit('update:modelValue', 'transfer')">
      <slot name="transferIcon"/>
      {{ transferLabel }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  modelValue:    { type: String, default: 'expense' },
  incomeLabel:   { type: String, default: 'Income' },
  expenseLabel:  { type: String, default: 'Expense' },
  transferLabel: { type: String, default: 'Transfer' },
});
defineEmits(['update:modelValue']);
</script>

<style scoped>
.tns-tt {
  display: grid; grid-template-columns: 1fr 1fr 1fr;
  gap: 6px; margin-bottom: 14px;
  font-family: var(--tns-font);
}
.tns-tt-btn {
  padding: 12px; border-radius: var(--tns-radius-md);
  border: none; background: var(--tns-bg); color: var(--tns-fg2);
  font-size: 14px; font-weight: 600; cursor: pointer;
  font-family: inherit;
  display: flex; align-items: center; justify-content: center; gap: 6px;
}
.tns-tt-btn :deep(svg) { width: 16px; height: 16px; }

.tns-tt-btn.active.income   { background: rgba(22,163,74,0.14);   color: var(--tns-green); }
.tns-tt-btn.active.expense  { background: rgba(220,38,38,0.14);   color: var(--tns-red); }
.tns-tt-btn.active.transfer { background: rgba(99,102,241,0.14);  color: var(--tns-accent); }
</style>
