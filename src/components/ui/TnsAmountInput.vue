<template>
  <!-- TnsAmountInput — big centered amount field used in transaction forms.
       Color-coded by transaction type (green income, red expense). -->
  <input
    class="tns-amt"
    :class="type"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    :placeholder="placeholder"
    inputmode="decimal"
  />
</template>

<script setup>
defineProps({
  modelValue:  { type: [String, Number], default: '' },
  /** 'income' | 'expense' — toggles colour. */
  type:        { type: String, default: 'expense' },
  placeholder: { type: String, default: '0,00 €' },
});
defineEmits(['update:modelValue']);
</script>

<style scoped>
.tns-amt {
  width: 100%; border: none; outline: none; background: transparent;
  font-family: var(--tns-font);
  font-size: 40px; font-weight: 700;
  text-align: center; padding: 14px 0;
  font-variant-numeric: tabular-nums; letter-spacing: -1px;
  color: var(--tns-fg);
}
.tns-amt.income  { color: var(--tns-green); }
.tns-amt.expense { color: var(--tns-red); }
.tns-amt::placeholder { color: var(--tns-fg3); }
</style>
