<script setup>
import { useI18n } from 'vue-i18n';
import { useFormat } from '@/composables/useFormat';

const { t } = useI18n();
const { fmt } = useFormat();

defineProps({
  account:     { type: Object, required: true },
  realBalance: { type: Number, default: null },
});
defineEmits(['click']);
</script>

<template>
  <div class="tns-acc" @click="$emit('click', account)">
    <div class="tns-acc-ico" :style="{ background: account.color }">
      <slot name="icon" />
    </div>
    <div class="tns-acc-main">
      <div class="tns-acc-title">{{ account.label }}</div>
      <div class="tns-acc-sub">{{ t(`accounts.type.${account.type}`) }}</div>
    </div>
    <div class="tns-acc-amt">{{ fmt(realBalance !== null ? realBalance : account.balance, account.currency) }}</div>
    <div class="tns-acc-chev"><slot name="chevron" /></div>
  </div>
</template>

<style scoped>
.tns-acc {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 14px; background: var(--tns-card);
  font-family: var(--tns-font); cursor: pointer;
}
.tns-acc + .tns-acc { border-top: 0.5px solid var(--tns-sep); }

.tns-acc-ico {
  width: 36px; height: 36px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  color: #fff; flex-shrink: 0;
}
.tns-acc-ico :deep(svg) { width: 18px; height: 18px; }

.tns-acc-main { flex: 1; min-width: 0; }
.tns-acc-title {
  font-size: 15px; font-weight: 500; color: var(--tns-fg);
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.tns-acc-sub { font-size: 12.5px; color: var(--tns-fg2); margin-top: 1px; }

.tns-acc-amt {
  font-size: 15px; font-weight: 600;
  font-variant-numeric: tabular-nums; letter-spacing: -0.3px;
  color: var(--tns-fg);
}
.tns-acc-chev { color: var(--tns-fg3); display: flex; }
.tns-acc-chev :deep(svg) { width: 16px; height: 16px; }
</style>
