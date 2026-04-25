<template>
  <!-- TnsTransactionRow — single transaction row for grouped lists.
       Handles coloured icon (from budget), title, meta (date · location),
       and signed amount (green for income, red for expense). -->
  <div class="tns-row" @click="$emit('click', transaction)">
    <div class="tns-row-ico" :style="{ background: iconColor }">
      <slot name="icon"/>
    </div>
    <div class="tns-row-main">
      <div class="tns-row-title">{{ transaction.label }}</div>
      <div class="tns-row-sub">
        <template v-if="showDate">{{ fmtDateShort(transaction.date) }} · </template>
        {{ transaction.location || transaction.category || accountLabel }}
      </div>
    </div>
    <div class="tns-row-amt" :class="transaction.type === 'income' ? 'pos' : 'neg'">
      {{ transaction.type === 'income' ? '+' : '' }}{{ fmt(transaction.amount, currency) }}
    </div>
  </div>
</template>

<script setup>
import { useFormat } from '@/composables/useFormat.ts';
const { fmt, fmtDateShort } = useFormat();

defineProps({
  /** Transaction shape: { id, label, amount, type: 'income'|'expense',
   *  date, location?, category?, budget_id?, account_id, is_recurring? }. */
  transaction:  { type: Object, required: true },
  /** Currency code of the parent account (EUR, USD, BTC…). */
  currency:     { type: String, default: 'EUR' },
  /** Hex/CSS colour for the round icon tile — usually budget.color. */
  iconColor:    { type: String, default: '#6B7280' },
  /** Account label shown when location/category are missing. */
  accountLabel: { type: String, default: '' },
  /** Show the short date in the sub-label (transactions list = true,
   *  account detail where day is already a section header = false). */
  showDate:     { type: Boolean, default: true },
});

defineEmits(['click']);
</script>

<style scoped>
.tns-row {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 14px; background: var(--tns-card);
  font-family: var(--tns-font); cursor: pointer;
}
.tns-row + .tns-row { border-top: 0.5px solid var(--tns-sep); }

.tns-row-ico {
  width: 36px; height: 36px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  color: #fff; flex-shrink: 0;
}
.tns-row-ico :deep(svg) { width: 18px; height: 18px; }

.tns-row-main { flex: 1; min-width: 0; }
.tns-row-title {
  font-size: 15px; font-weight: 500; color: var(--tns-fg);
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.tns-row-sub { font-size: 12.5px; color: var(--tns-fg2); margin-top: 1px; }

.tns-row-amt {
  font-size: 15px; font-weight: 600;
  font-variant-numeric: tabular-nums; text-align: right;
  letter-spacing: -0.3px;
}
.tns-row-amt.pos { color: var(--tns-green); }
.tns-row-amt.neg { color: var(--tns-red); }
</style>
