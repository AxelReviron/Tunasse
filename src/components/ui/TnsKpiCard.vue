<template>
  <!-- TnsKpiCard — small card showing a metric with an icon + colored tone.
       Used on Dashboard "This month" grid (2 or 4 columns). -->
  <div class="tns-kpi" :class="tone">
    <div class="tns-kpi-head">
      <slot name="icon"/>
      <span class="tns-kpi-heading">{{ label }}</span>
    </div>
    <div class="tns-kpi-val">{{ value }}</div>
  </div>
</template>

<script setup>
defineProps({
  label: { type: String, required: true },
  value: { type: [String, Number], required: true },
  /** 'neutral' | 'green' | 'red' | 'orange' — tints the header label + icon. */
  tone:  { type: String, default: 'neutral' },
});
</script>

<style scoped>
.tns-kpi {
  background: var(--tns-card);
  border-radius: var(--tns-radius-lg);
  padding: 12px 13px;
  font-family: var(--tns-font);
}
.tns-kpi-head {
  display: flex; align-items: center; gap: 6px;
  font-size: 11.5px; font-weight: 600; color: var(--tns-fg2);
  letter-spacing: -0.1px;
}
.tns-kpi-heading {
  font-size: 14px; font-weight: 600;
}
.tns-kpi-head :deep(svg) { width: 14px; height: 14px; flex-shrink: 0; }
.tns-kpi-val {
  font-size: 19px; font-weight: 700; margin-top: 6px;
  font-variant-numeric: tabular-nums; letter-spacing: -0.4px;
  color: var(--tns-fg);
}
.tns-kpi.green  .tns-kpi-head { color: var(--tns-green); }
.tns-kpi.red    .tns-kpi-head { color: var(--tns-red); }
.tns-kpi.orange .tns-kpi-head { color: var(--tns-orange); }
</style>
