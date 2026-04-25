<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { IonPage, IonContent, IonIcon } from '@ionic/vue';
import {
  checkmarkCircleOutline, alertCircleOutline, trendingUpOutline,
  trendingDownOutline, cartOutline, homeOutline, carOutline,
  restaurantOutline, repeatOutline,
} from 'ionicons/icons';

import TnsLargeTitle     from '@/components/ui/TnsLargeTitle.vue';
import TnsKpiCard        from '@/components/ui/TnsKpiCard.vue';
import TnsSectionHeader  from '@/components/ui/TnsSectionHeader.vue';
import TnsSectionTitle   from '@/components/ui/TnsSectionTitle.vue';
import TnsList           from '@/components/ui/TnsList.vue';
import TnsTransactionRow from '@/components/ui/TnsTransactionRow.vue';
import TnsBudgetProgress from '@/components/ui/TnsBudgetProgress.vue';
import BarChart          from '@/components/BarChart.vue';
import PieChart          from '@/components/PieChart.vue';
import LineChart         from '@/components/LineChart.vue';
import { useFormat }     from '@/composables/useFormat';
import type { Account, Budget, Transaction } from '@/types';

const { t } = useI18n();
const { fmt, fmtShort } = useFormat();

// ─── Mock data ────────────────────────────────────────────────────────────────
const accounts = ref<Account[]>([
  { id: 1, label: 'Compte courant', currency: 'EUR', iban: '****1234', type: 'checking', balance: 3200, color: '#6366F1' },
  { id: 2, label: 'Livret A',       currency: 'EUR', iban: '****5678', type: 'savings',  balance: 8000, color: '#22C55E' },
]);

const budgets = ref<Budget[]>([
  { id: 1, label: 'Courses',    color: '#F97316', amount: 400, spent: 230, currency: 'EUR' },
  { id: 2, label: 'Restaurant', color: '#F43F5E', amount: 150, spent: 90,  currency: 'EUR' },
  { id: 3, label: 'Logement',   color: '#6366F1', amount: 900, spent: 900, currency: 'EUR' },
  { id: 4, label: 'Transport',  color: '#0EA5E9', amount: 100, spent: 60,  currency: 'EUR' },
]);

const transactions = ref<Transaction[]>([
  { id: 1, label: 'Carrefour',        amount: 54.30,  type: 'expense', date: '2026-04-23', account_id: 1, budget_id: 1, category: 'Courses' },
  { id: 2, label: 'Salaire avril',    amount: 2800,   type: 'income',  date: '2026-04-23', account_id: 1 },
  { id: 3, label: 'Sushi Shop',       amount: 32.00,  type: 'expense', date: '2026-04-22', account_id: 1, budget_id: 2, category: 'Restaurant' },
  { id: 4, label: 'Loyer',            amount: 900,    type: 'expense', date: '2026-04-22', account_id: 1, budget_id: 3, category: 'Logement', is_recurring: true, recurring_unit: 'month' },
  { id: 5, label: 'Lidl',             amount: 38.10,  type: 'expense', date: '2026-04-21', account_id: 1, budget_id: 1, category: 'Courses' },
  { id: 6, label: 'Navigo mensuel',   amount: 86.40,  type: 'expense', date: '2026-04-20', account_id: 1, budget_id: 4, category: 'Transport', is_recurring: true, recurring_unit: 'month' },
  { id: 7, label: 'Virement épargne', amount: 200,    type: 'expense', date: '2026-04-20', account_id: 1 },
]);
// ───────────────────────────────────────────────────────────��─────────────────

const totalBalance = computed(() =>
  accounts.value.reduce((sum, a) => sum + a.balance, 0)
);
const monthIncome = computed(() =>
  transactions.value.filter(t => t.type === 'income').reduce((s, t) => s + t.amount, 0)
);
const monthExpense = computed(() =>
  transactions.value.filter(t => t.type === 'expense').reduce((s, t) => s + t.amount, 0)
);
const recentTx = computed(() =>
  [...transactions.value].sort((a, b) => b.date.localeCompare(a.date)).slice(0, 4)
);

function accountOf(id: number) { return accounts.value.find(a => a.id === id); }
function budgetOf(id?: number) { return id ? budgets.value.find(b => b.id === id) : undefined; }

const ICON_MAP: Record<string, unknown> = {
  Courses: cartOutline, Restaurant: restaurantOutline,
  Logement: homeOutline, Transport: carOutline,
};
function iconFor(tx: { type: string; category?: string; is_recurring?: boolean }) {
  if (tx.type === 'income') return trendingUpOutline;
  if (tx.is_recurring)      return repeatOutline;
  return ICON_MAP[tx.category ?? ''] ?? cartOutline;
}

const barLabels   = computed(() => accounts.value.map(a => a.label));
const barDatasets = computed(() => [{
  label: t('accounts.balance'),
  data:  accounts.value.map(a => a.balance),
  backgroundColor: accounts.value.map(a => a.color + '99'),
  borderColor:     accounts.value.map(a => a.color),
  borderRadius: 8,
  borderWidth: 1.5,
}]);

const pieLabels = computed(() => budgets.value.map(b => b.label));
const pieData   = computed(() => budgets.value.map(b => b.spent));
const pieColors = computed(() => budgets.value.map(b => b.color));

const lineLabels = computed(() => {
  const now = new Date();
  const days = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
  return Array.from({ length: days }, (_, i) => String(i + 1).padStart(2, '0'));
});
const lineDatasets = computed(() => {
  const now   = new Date();
  const year  = now.getFullYear();
  const month = now.getMonth();
  const days  = new Date(year, month + 1, 0).getDate();
  const daily = new Array(days).fill(0);

  transactions.value.forEach(tx => {
    const d = new Date(tx.date);
    if (d.getFullYear() === year && d.getMonth() === month) {
      daily[d.getDate() - 1] += tx.type === 'income' ? tx.amount : -tx.amount;
    }
  });

  const cumul = daily.reduce<number[]>((acc, v, i) => {
    acc.push((acc[i - 1] ?? 0) + v);
    return acc;
  }, []);

  return [{
    label: t('dashboard.cumulativeBalance'),
    data: cumul,
    borderColor: '#6366F1',
    backgroundColor: 'rgba(99,102,241,0.15)',
    fill: true,
    tension: 0.4,
    pointRadius: 3,
    pointBackgroundColor: '#6366F1',
  }];
});
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" :style="{ '--background': 'var(--tns-bg)' }">
      <div class="tns-page">

        <TnsLargeTitle :title="t('dashboard.title')" />

        <div class="tns-balance">
          <div class="tns-balance-label">{{ t('dashboard.totalBalance') }}</div>
          <div class="tns-balance-amount">{{ fmt(totalBalance, 'EUR') }}</div>
        </div>

        <TnsSectionTitle :title="t('dashboard.monthlyOverview')" />
        <div class="tns-kpi-grid">
          <TnsKpiCard :label="t('transactions.income')" :value="fmtShort(monthIncome, 'EUR')" tone="green">
            <template #icon><ion-icon :icon="trendingUpOutline" /></template>
          </TnsKpiCard>
          <TnsKpiCard :label="t('transactions.expense')" :value="fmtShort(monthExpense, 'EUR')" tone="red">
            <template #icon><ion-icon :icon="trendingDownOutline" /></template>
          </TnsKpiCard>
          <TnsKpiCard :label="t('dashboard.alreadyPaid')" :value="fmtShort(monthExpense, 'EUR')" tone="neutral">
            <template #icon><ion-icon :icon="checkmarkCircleOutline" /></template>
          </TnsKpiCard>
          <TnsKpiCard :label="t('budgets.remaining')" :value="fmtShort(monthIncome - monthExpense, 'EUR')" tone="orange">
            <template #icon><ion-icon :icon="alertCircleOutline" /></template>
          </TnsKpiCard>
        </div>

        <!-- Charts : Bar + Pie côte à côte sur desktop -->
        <div class="tns-charts-row">
          <div class="tns-chart-card">
            <TnsSectionTitle :title="t('dashboard.balanceByAccount')" />
            <BarChart :labels="barLabels" :datasets="barDatasets" y-tick-suffix=" €" />
          </div>
          <div class="tns-chart-card">
            <TnsSectionTitle :title="t('dashboard.expenseBreakdown')" />
            <PieChart :labels="pieLabels" :data="pieData" :colors="pieColors" :height="200" />
          </div>
        </div>

        <!-- Line chart pleine largeur -->
        <div class="tns-chart-card">
          <TnsSectionTitle :title="t('dashboard.monthlyFlow')" />
          <LineChart :labels="lineLabels" :datasets="lineDatasets" y-tick-suffix=" €" />
        </div>

        <TnsSectionHeader :label="t('nav.budgets')">
          <template #action>{{ t('common.seeAll') }}</template>
        </TnsSectionHeader>
        <TnsList>
          <div v-for="b in budgets" :key="b.id" class="tns-budget-row">
            <div class="tns-budget-icon" :style="{ background: b.color }">
              <ion-icon :icon="ICON_MAP[b.label] ?? cartOutline" />
            </div>
            <div class="tns-budget-body">
              <div class="tns-budget-label">{{ b.label }}</div>
              <TnsBudgetProgress :spent="b.spent" :amount="b.amount" :color="b.color" :currency="b.currency" />
            </div>
          </div>
        </TnsList>

        <TnsSectionHeader :label="t('dashboard.recentTransactions')">
          <template #action>{{ t('common.seeAll') }}</template>
        </TnsSectionHeader>
        <TnsList>
          <TnsTransactionRow
            v-for="tx in recentTx"
            :key="tx.id"
            :transaction="tx"
            :currency="accountOf(tx.account_id)?.currency || 'EUR'"
            :icon-color="budgetOf(tx.budget_id)?.color || '#6B7280'"
            :account-label="accountOf(tx.account_id)?.label || ''"
            :show-date="true"
          >
            <template #icon>
              <ion-icon :icon="iconFor(tx)" />
            </template>
          </TnsTransactionRow>
        </TnsList>

      </div>
    </ion-content>
  </ion-page>
</template>

<style scoped>
/* ── Balance hero ─────────────────────────────────────────────────────────── */
.tns-balance {
  margin: 0 16px 20px;
  background: linear-gradient(135deg, var(--tns-accent), var(--tns-accent-soft));
  border-radius: var(--tns-radius-xl);
  padding: 24px;
  font-family: var(--tns-font);
}
.tns-balance-label {
  font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.7);
}
.tns-balance-amount {
  font-size: 36px; font-weight: 700; color: #fff;
  letter-spacing: -1px; margin-top: 4px;
  font-variant-numeric: tabular-nums;
}

/* ── KPI grid ─────────────────────���───────────────────────��──────────────── */
.tns-kpi-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--tns-gap);
  padding: 0 16px;
  margin-bottom: 14px;
}

@media (min-width: 768px) {
  .tns-kpi-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

/* ── Charts ──────────────────────────────────────────────────────────────── */
.tns-chart-card {
  margin: 0 16px 14px;
  background: var(--tns-card);
  border-radius: var(--tns-radius-lg);
  padding: 16px 16px 12px;
}

.tns-charts-row {
  display: flex;
  flex-direction: column;
  margin: 0 16px 14px;
  gap: 14px;
}

.tns-charts-row .tns-chart-card {
  margin: 0;
}

@media (min-width: 768px) {
  .tns-charts-row {
    flex-direction: row;
  }

  .tns-charts-row .tns-chart-card {
    flex: 1;
  }
}

/* ── Budget rows ─────────────────────────────────────────────────────────── */
.tns-budget-row {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 14px;
}
.tns-budget-row + .tns-budget-row { border-top: 0.5px solid var(--tns-sep); }
.tns-budget-icon {
  width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; color: #fff;
}
.tns-budget-icon :deep(svg) { width: 18px; height: 18px; }
.tns-budget-body { flex: 1; min-width: 0; }
.tns-budget-label {
  font-size: 15px; font-weight: 500;
  color: var(--tns-fg); font-family: var(--tns-font);
}
</style>
