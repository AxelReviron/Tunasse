<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonIcon } from '@ionic/vue';
import {
  checkmarkCircleOutline, alertCircleOutline, trendingUpOutline,
  trendingDownOutline, cartOutline, homeOutline, carOutline,
  restaurantOutline, repeatOutline, calendarClearOutline, barChartOutline, fileTrayFullOutline, layersOutline,
  readerOutline, pieChartOutline
} from 'ionicons/icons';

import TnsLargeTitle     from '@/components/ui/TnsLargeTitle.vue';
import TnsKpiCard        from '@/components/ui/TnsKpiCard.vue';
import TnsSectionTitle   from '@/components/ui/TnsSectionTitle.vue';
import TnsList           from '@/components/ui/TnsList.vue';
import TnsTransactionRow from '@/components/ui/TnsTransactionRow.vue';
import TnsBudgetProgress from '@/components/ui/TnsBudgetProgress.vue';
import BarChart          from '@/components/BarChart.vue';
import PieChart          from '@/components/PieChart.vue';
import LineChart         from '@/components/LineChart.vue';
import { useFormat }     from '@/composables/useFormat';
import { useAccounts }     from '@/composables/useAccounts';
import { useTransactions } from '@/composables/useTransactions';
import { useBudgets }      from '@/composables/useBudgets';

const { t } = useI18n();
const { fmt, fmtShort } = useFormat();
const router = useRouter();

const { accounts, totalBalance, getById: accountOf } = useAccounts();
const { transactions, monthIncome, monthExpense, recent: recentTx } = useTransactions();
const { budgets, getById: budgetOf } = useBudgets();

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
const pieData   = computed(() => budgets.value.map(b => b.spent ?? 0));
const pieColors = computed(() => budgets.value.map(b => b.color));

const lineChart = computed(() => {
  const now    = new Date();
  const prefix = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;

  const dailyMap: Record<string, number> = {};
  transactions.value.forEach(tx => {
    if (tx.date.startsWith(prefix))
      dailyMap[tx.date] = (dailyMap[tx.date] ?? 0) + (tx.type === 'income' ? tx.amount : -tx.amount);
  });

  const dates = Object.keys(dailyMap).sort();

  let cumul = 0;
  const data = dates.map(date => {
    cumul += dailyMap[date];
    return cumul;
  });

  return {
    labels: dates.map(d => d.slice(8, 10)),
    datasets: [{
      label: t('dashboard.cumulativeBalance'),
      data,
      borderColor: '#6366F1',
      backgroundColor: 'rgba(99,102,241,0.15)',
      fill: true,
      tension: 0.4,
      pointRadius: 4,
      pointBackgroundColor: '#6366F1',
    }],
  };
});

const lineLabels   = computed(() => lineChart.value.labels);
const lineDatasets = computed(() => lineChart.value.datasets);
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" :style="{ '--background': 'var(--tns-bg)' }">
      <div class="tns-page">

        <TnsLargeTitle :title="t('dashboard.title')" />

        <!-- Balance hero — titre en label interne -->
        <div class="tns-balance">
          <div class="tns-balance-label">{{ t('dashboard.totalBalance') }}</div>
          <template v-if="totalBalance.length">
            <div
              v-for="entry in totalBalance"
              :key="entry.currency"
              class="tns-balance-amount"
            >{{ fmt(entry.total, entry.currency) }}</div>
          </template>
          <div v-else class="tns-balance-empty">{{ t('accounts.empty') }}</div>
        </div>

        <!-- KPI — titre au-dessus (exception : pas de card englobante) -->
        <div class="tns-section">
          <div class="tns-section-header-row">
            <ion-icon :icon="fileTrayFullOutline" />
            <TnsSectionTitle :title="t('dashboard.monthlyOverview')" />
          </div>
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
        </div>

        <!-- Bar + Pie côte à côte — titre au-dessus de chaque card -->
        <div class="tns-charts-row">
          <div class="tns-chart-section">
          <div class="tns-section-header-row">
              <ion-icon :icon="barChartOutline"/>
              <TnsSectionTitle :title="t('dashboard.balanceByAccount')" />
          </div>
            <div class="tns-chart-card">
              <BarChart v-if="accounts.length" :labels="barLabels" :datasets="barDatasets" y-tick-suffix=" €" />
              <div v-else class="tns-chart-empty">{{ t('accounts.empty') }}</div>
            </div>
          </div>
          <div class="tns-chart-section">
          <div class="tns-section-header-row">
              <ion-icon :icon="pieChartOutline"/>
              <TnsSectionTitle :title="t('dashboard.expenseBreakdown')" />
          </div>
            <div class="tns-chart-card">
              <PieChart v-if="budgets.length" :labels="pieLabels" :data="pieData" :colors="pieColors" :height="200" />
              <div v-else class="tns-chart-empty">{{ t('budgets.empty') }}</div>
            </div>
          </div>
        </div>

        <!-- Line chart — titre au-dessus -->
        <div class="tns-chart-section">
          <div class="tns-section-header-row">
            <ion-icon :icon="calendarClearOutline"/>
            <TnsSectionTitle :title="t('dashboard.monthlyFlow')" />
          </div>
          <div class="tns-chart-card">
            <LineChart v-if="lineLabels.length" :labels="lineLabels" :datasets="lineDatasets" y-tick-suffix=" €" />
            <div v-else class="tns-chart-empty">{{ t('transactions.empty') }}</div>
          </div>
        </div>

        <div class="tns-list-hdr">
          <div class="tns-section-header-row">
            <ion-icon :icon="layersOutline"/>
            <span class="tns-list-hdr-title">{{ t('nav.budgets') }}</span>
          </div>
          <span class="tns-list-hdr-action" @click="router.push('/tabs/budget')">{{ t('common.seeAll') }}</span>
        </div>
        <TnsList>
          <template v-if="budgets.length">
            <div v-for="b in budgets" :key="b.id" class="tns-budget-row">
              <div class="tns-budget-icon" :style="{ background: b.color }">
                <ion-icon :icon="ICON_MAP[b.label] ?? cartOutline" />
              </div>
              <div class="tns-budget-body">
                <div class="tns-budget-label">{{ b.label }}</div>
                <TnsBudgetProgress :spent="b.spent" :amount="b.amount" :color="b.color" :currency="b.currency" />
              </div>
            </div>
          </template>
          <div v-else class="tns-list-empty">{{ t('budgets.empty') }}</div>
        </TnsList>

        <div class="tns-list-hdr">
          <div class="tns-section-header-row">
            <ion-icon :icon="readerOutline"/>
            <span class="tns-list-hdr-title">{{ t('dashboard.recentTransactions') }}</span>
          </div>
          <span class="tns-list-hdr-action" @click="router.push('/tabs/transactions')">{{ t('common.seeAll') }}</span>
        </div>
        <TnsList>
          <template v-if="recentTx.length">
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
          </template>
          <div v-else class="tns-list-empty">{{ t('transactions.empty') }}</div>
        </TnsList>

      </div>
    </ion-content>
  </ion-page>
</template>

<style scoped>
.tns-page {
  margin-top: 16px;
}
/* ── Balance hero ─────────────────────────────────────────────────────────── */
.tns-balance {
  margin: 0 16px 24px;
  background: var(--tns-card);
  border-radius: var(--tns-radius-xl);
  padding: 24px;
  font-family: var(--tns-font);
}
.tns-balance-label {
  font-size: 13px; font-weight: 500; color: var(--tns-fg2);
}
.tns-balance-amount {
  font-size: 36px; font-weight: 700; color: var(--tns-accent);
  letter-spacing: -1px; margin-top: 4px;
  font-variant-numeric: tabular-nums;
}

/* ── KPI grid ──────────────────────────────────────────────────────────── */
/* ── KPI section ──────────────────────────────────────────────────────────── */
.tns-section {
  padding: 0 16px;
  margin-bottom: 24px;
}

.tns-section-header-row {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 6px;
  margin-top: 16px;
  margin-bottom: 8px;
  font-size: 22px;
}

.tns-kpi-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--tns-gap);
}

@media (min-width: 768px) {
  .tns-kpi-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

/* ── Charts ──────────────────────────────────────────────────────────────── */
.tns-chart-section {
  margin: 0 16px 24px;
}

.tns-chart-card {
  background: var(--tns-card);
  border-radius: var(--tns-radius-lg);
  padding: 16px 16px 12px;
}

.tns-charts-row {
  display: flex;
  flex-direction: column;
  margin: 0 16px 24px;
  gap: 14px;
}

.tns-charts-row .tns-chart-section {
  margin: 0;
  flex: 1;
}

@media (min-width: 768px) {
  .tns-charts-row {
    flex-direction: row;
  }
}

/* ── List section headers ────────────────────────────────────────────────── */
.tns-list-hdr {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  padding: 0 16px;
  margin-bottom: 8px;
}
.tns-list-hdr-title {
  font-family: var(--tns-font);
  font-size: 18px;
  font-weight: 600;
  color: var(--tns-fg);
  letter-spacing: -0.2px;
}
.tns-list-hdr-action {
  font-family: var(--tns-font);
  font-size: 13px;
  font-weight: 500;
  color: var(--tns-accent);
  cursor: pointer;
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

/* ── Empty states ────────────────────────────────────────────────────────── */
.tns-balance-empty {
  font-size: 22px; font-weight: 600; color: var(--tns-fg3);
  margin-top: 4px;
}
.tns-chart-empty,
.tns-list-empty {
  text-align: center;
  padding: 24px 16px;
  font-family: var(--tns-font);
  font-size: 14px;
  color: var(--tns-fg3);
}
</style>
