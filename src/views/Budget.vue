<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { IonPage, IonContent, IonIcon } from '@ionic/vue';
import { addOutline } from 'ionicons/icons';

import TnsLargeTitle    from '@/components/ui/TnsLargeTitle.vue';
import TnsList          from '@/components/ui/TnsList.vue';
import TnsBudgetProgress from '@/components/ui/TnsBudgetProgress.vue';
import TnsBudgetSheet   from '@/components/TnsBudgetSheet.vue';
import { useBudgets }   from '@/composables/useBudgets';
import { useFormat }    from '@/composables/useFormat';
import { ICON_MAP }     from '@/constants/icons';
import type { Budget }  from '@/types';

const { t } = useI18n();
const { fmtShort } = useFormat();
const { budgets, totalAllocated, totalSpent } = useBudgets();

const showSheet     = ref(false);
const selectedBudget = ref<Budget | undefined>(undefined);

function openCreate() {
  selectedBudget.value = undefined;
  showSheet.value = true;
}

function openEdit(budget: Budget) {
  selectedBudget.value = budget;
  showSheet.value = true;
}

function onSheetClose() {
  showSheet.value = false;
  selectedBudget.value = undefined;
}

function iconFor(budget: Budget) {
  return budget.icon && ICON_MAP[budget.icon] ? ICON_MAP[budget.icon] : null
}
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" :style="{ '--background': 'var(--tns-bg)' }">
      <div class="tns-page">

        <TnsLargeTitle :title="t('budgets.title')" />

        <!-- KPI résumé -->
        <div v-if="budgets.length" class="tns-summary">
          <div class="tns-summary-item">
            <div class="tns-summary-label">{{ t('budgets.spent') }}</div>
            <div class="tns-summary-value red">{{ fmtShort(totalSpent, 'EUR') }}</div>
          </div>
          <div class="tns-summary-sep" />
          <div class="tns-summary-item">
            <div class="tns-summary-label">{{ t('budgets.remaining') }}</div>
            <div class="tns-summary-value" :class="totalAllocated - totalSpent < 0 ? 'red' : 'green'">
              {{ fmtShort(totalAllocated - totalSpent, 'EUR') }}
            </div>
          </div>
          <div class="tns-summary-sep" />
          <div class="tns-summary-item">
            <div class="tns-summary-label">{{ t('budgets.monthlyCap') }}</div>
            <div class="tns-summary-value">{{ fmtShort(totalAllocated, 'EUR') }}</div>
          </div>
        </div>

        <TnsList v-if="budgets.length">
          <div
            v-for="b in budgets"
            :key="b.id"
            class="tns-budget-row"
            @click="openEdit(b)"
          >
            <div class="tns-budget-icon" :style="{ background: b.color }">
              <ion-icon v-if="iconFor(b)" :icon="iconFor(b)" />
            </div>
            <div class="tns-budget-body">
              <div class="tns-budget-label">{{ b.label }}</div>
              <TnsBudgetProgress
                :spent="b.spent"
                :amount="b.amount"
                :color="b.color"
                :currency="b.currency"
              />
            </div>
          </div>
        </TnsList>

        <div v-else class="tns-empty">{{ t('budgets.empty') }}</div>

      </div>

      <button class="tns-fab" @click="openCreate">
        <ion-icon :icon="addOutline" />
      </button>

    </ion-content>

    <TnsBudgetSheet
      v-model="showSheet"
      :budget="selectedBudget"
      @saved="onSheetClose"
      @deleted="onSheetClose"
    />
  </ion-page>
</template>

<style scoped>
.tns-page { margin-top: 16px; }

/* ── Résumé KPI ───────────────────────────────────────────────────────────── */
.tns-summary {
  display: flex;
  align-items: center;
  margin: 0 16px 20px;
  background: var(--tns-card);
  border-radius: var(--tns-radius-xl);
  padding: 16px;
  font-family: var(--tns-font);
}
.tns-summary-item {
  flex: 1;
  text-align: center;
}
.tns-summary-label {
  font-size: 11.5px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.2px;
  color: var(--tns-fg2);
  margin-bottom: 4px;
}
.tns-summary-value {
  font-size: 18px;
  font-weight: 700;
  color: var(--tns-fg);
  font-variant-numeric: tabular-nums;
  letter-spacing: -0.3px;
}
.tns-summary-value.red   { color: var(--tns-red); }
.tns-summary-value.green { color: var(--tns-green); }
.tns-summary-sep {
  width: 0.5px;
  height: 32px;
  background: var(--tns-sep);
}

/* ── Budget rows ─────────────────────────────────────────────────────────── */
.tns-budget-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  cursor: pointer;
}
.tns-budget-row + .tns-budget-row { border-top: 0.5px solid var(--tns-sep); }

.tns-budget-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 20px;
}

.tns-budget-body { flex: 1; min-width: 0; }
.tns-budget-label {
  font-size: 15px;
  font-weight: 500;
  color: var(--tns-fg);
  font-family: var(--tns-font);
  margin-bottom: 2px;
}

/* ── Empty / FAB ─────────────────────────────────────────────────────────── */
.tns-empty {
  text-align: center;
  padding: 48px 16px;
  color: var(--tns-fg2);
  font-family: var(--tns-font);
  font-size: 15px;
}
.tns-fab {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: var(--tns-accent);
  color: #fff;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
  z-index: 10;
  font-size: 24px;
}
</style>
