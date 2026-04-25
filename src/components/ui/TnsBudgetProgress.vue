<template>
  <!-- TnsBudgetProgress — horizontal bar + meta for budget progression.
       Switches to red when > warnAt (default 90 %). -->
  <div class="tns-bp">
    <div class="tns-bp-bar">
      <div class="tns-bp-fill"
           :style="{ width: pct + '%', background: over ? 'var(--tns-red)' : color }"/>
    </div>
    <div class="tns-bp-meta">
      <span>{{ fmtShort(spent, currency) }} / {{ fmtShort(amount, currency) }}</span>
      <span :style="{ color: over ? 'var(--tns-red)' : 'var(--tns-fg2)' }">{{ pct }} %</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useFormat } from '../../composables/useFormat.js';
const { fmtShort } = useFormat();

const props = defineProps({
  spent:    { type: Number, required: true },
  amount:   { type: Number, required: true },
  currency: { type: String, default: 'EUR' },
  color:    { type: String, default: '#4F46E5' },  // budget.color
  warnAt:   { type: Number, default: 90 },         // percentage threshold
});

const pct  = computed(() => Math.min(100, Math.round((props.spent / props.amount) * 100 || 0)));
const over = computed(() => pct.value >= props.warnAt);
</script>

<style scoped>
.tns-bp { font-family: var(--tns-font); }
.tns-bp-bar {
  height: 6px; background: rgba(120,120,128,0.14);
  border-radius: 3px; overflow: hidden; margin-top: 10px;
}
.tns-bp-fill { height: 100%; border-radius: 3px; transition: width .4s ease; }
.tns-bp-meta {
  display: flex; justify-content: space-between;
  font-size: 12px; color: var(--tns-fg2);
  margin-top: 8px; font-variant-numeric: tabular-nums;
}
</style>
