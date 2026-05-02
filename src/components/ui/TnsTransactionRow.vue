<script setup>
import { IonIcon } from '@ionic/vue';
import { timeOutline } from 'ionicons/icons';
import { useFormat } from '@/composables/useFormat.ts';

const { fmt, fmtDateShort } = useFormat();

defineProps({
  transaction:    { type: Object,  required: true },
  currency:       { type: String,  default: 'EUR' },
  iconColor:      { type: String,  default: '#6B7280' },
  accountLabel:   { type: String,  default: '' },
  toAccountLabel: { type: String,  default: '' },
  showDate:       { type: Boolean, default: true },
  /** Transaction dont la date est dans le futur — opacité réduite + badge horloge. */
  future:         { type: Boolean, default: false },
});

defineEmits(['click']);
</script>

<template>
  <div class="tns-row" :class="{ 'tns-row--future': future }" @click="$emit('click', transaction)">
    <div class="tns-row-ico" :style="{ background: iconColor }">
      <slot name="icon"/>
      <span v-if="future" class="tns-future-badge">
        <ion-icon :icon="timeOutline" />
      </span>
    </div>
    <div class="tns-row-main">
      <div class="tns-row-title">{{ transaction.label }}</div>
      <div class="tns-row-sub">
        <template v-if="showDate">{{ fmtDateShort(transaction.date) }} · </template>
        <template v-if="transaction.transfer_peer_id !== undefined">
          {{ accountLabel }} → {{ toAccountLabel }}
        </template>
        <template v-else>{{ transaction.location || transaction.category || accountLabel }}</template>
      </div>
    </div>
    <div
      class="tns-row-amt"
      :class="transaction.transfer_peer_id !== undefined ? '' : transaction.type === 'income' ? 'pos' : 'neg'"
    >
      <template v-if="transaction.transfer_peer_id !== undefined">{{ fmt(transaction.amount, currency) }}</template>
      <template v-else>{{ transaction.type === 'income' ? '+' : '-' }}{{ fmt(transaction.amount, currency) }}</template>
    </div>
  </div>
</template>

<style scoped>
.tns-row {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 14px; background: var(--tns-card);
  font-family: var(--tns-font); cursor: pointer;
}
.tns-row + .tns-row { border-top: 0.5px solid var(--tns-sep); }

.tns-row--future { opacity: 0.45; }

.tns-row-ico {
  position: relative;
  width: 36px; height: 36px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  color: #fff; flex-shrink: 0;
}
.tns-row-ico :deep(svg) { width: 18px; height: 18px; }

.tns-future-badge {
  position: absolute;
  bottom: -5px; right: -5px;
  width: 16px; height: 16px;
  border-radius: 50%;
  background: var(--tns-bg);
  display: flex; align-items: center; justify-content: center;
  font-size: 11px;
  color: var(--tns-fg2);
}

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
