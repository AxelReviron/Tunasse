<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter, useRoute } from 'vue-router';
import { IonIcon } from '@ionic/vue';
import TnsPage from '@/components/ui/TnsPage.vue';
import {
  trendingUpOutline, trendingDownOutline, timeOutline,
  cartOutline, homeOutline, carOutline,
  restaurantOutline, repeatOutline, calendarClearOutline, barChartOutline, fileTrayFullOutline, layersOutline,
  readerOutline, pieChartOutline, swapHorizontalOutline,
  chevronBackOutline, chevronForwardOutline, helpCircleOutline,
} from 'ionicons/icons';

import TnsInfoModal      from '@/components/ui/TnsInfoModal.vue';
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
import { CURRENCY_SUBUNIT } from '@/constants/currencies';
import type { Currency } from '@/types';

const { t, locale } = useI18n();
const { fmt, fmtShort } = useFormat();
const router = useRouter();
const route  = useRoute();

const { accounts, totalBalance, realBalances, getById: accountOf } = useAccounts();
const { transactions, today, recent: recentTx } = useTransactions();
const { budgets, getById: budgetOf } = useBudgets();

// ── Modale d'aide ─────────────────────────────────────────────────────────
type HelpTopic = { title: string; body: string }
const helpTopic  = ref<HelpTopic | null>(null)
const showHelp   = computed(() => helpTopic.value !== null)
function openHelp(key: 'hero' | 'kpi' | 'balanceByAccount' | 'expenseBreakdown' | 'monthlyFlow') {
  helpTopic.value = { title: t(`dashboard.help.${key}.title`), body: t(`dashboard.help.${key}.body`) }
}
watch(() => route.path, () => { helpTopic.value = null })

// ── Navigation par mois ────────────────────────────────────────────────────
const _now = new Date();
const selectedMonth = ref({ year: _now.getFullYear(), month: _now.getMonth() + 1 });

const isCurrentMonth = computed(() =>
  selectedMonth.value.year === _now.getFullYear() && selectedMonth.value.month === _now.getMonth() + 1
);

function prevMonth() {
  const { year, month } = selectedMonth.value;
  selectedMonth.value = month === 1 ? { year: year - 1, month: 12 } : { year, month: month - 1 };
}
function nextMonth() {
  if (isCurrentMonth.value) return;
  const { year, month } = selectedMonth.value;
  selectedMonth.value = month === 12 ? { year: year + 1, month: 1 } : { year, month: month + 1 };
}
function goToCurrentMonth() {
  selectedMonth.value = { year: _now.getFullYear(), month: _now.getMonth() + 1 };
}

const periodLabel = computed(() => {
  const { year, month } = selectedMonth.value;
  const monthName = new Date(year, month - 1, 1).toLocaleDateString(locale.value, { month: 'long' });
  return `${monthName} ${year}`;
});

// ── Transactions de la période ─────────────────────────────────────────────
const selectedMonthPrefix = computed(() => {
  const { year, month } = selectedMonth.value;
  return `${year}-${String(month).padStart(2, '0')}`;
});

const selectedMonthTransactions = computed(() =>
  transactions.value.filter(tx => tx.date.startsWith(selectedMonthPrefix.value))
);

const periodIncome = computed(() =>
  selectedMonthTransactions.value
    .filter(tx => tx.type === 'income' && !tx.transfer_peer_id && tx.date <= today)
    .reduce((sum, tx) => sum + tx.amount, 0)
);

const periodExpense = computed(() =>
  selectedMonthTransactions.value
    .filter(tx => tx.type === 'expense' && !tx.transfer_peer_id && tx.date <= today)
    .reduce((sum, tx) => sum + tx.amount, 0)
);

const periodUpcoming = computed(() =>
  selectedMonthTransactions.value
    .filter(tx => tx.type === 'expense' && !tx.transfer_peer_id && tx.date > today)
    .reduce((sum, tx) => sum + tx.amount, 0)
);

const periodBudgetSpent = computed(() => {
  const result: Record<string, number> = {};
  selectedMonthTransactions.value
    .filter(tx => tx.budget_id && !tx.transfer_peer_id && tx.date <= today)
    .forEach(tx => { result[tx.budget_id!] = (result[tx.budget_id!] ?? 0) + tx.amount; });
  return result;
});

// ── Icônes ─────────────────────────────────────────────────────────────────
const ICON_MAP: Record<string, unknown> = {
  Courses: cartOutline, Restaurant: restaurantOutline,
  Logement: homeOutline, Transport: carOutline,
};
function iconFor(tx: { type: string; category?: string; is_recurring?: boolean; transfer_peer_id?: string }) {
  if (tx.transfer_peer_id !== undefined) return swapHorizontalOutline;
  if (tx.type === 'income') return trendingUpOutline;
  if (tx.is_recurring)      return repeatOutline;
  return ICON_MAP[tx.category ?? ''] ?? cartOutline;
}

// ── Bar chart (solde à la fin de la période sélectionnée) ─────────────────
// asOf = min(today, dernier jour du mois sélectionné)
const periodAsOf = computed(() => {
  const { year, month } = selectedMonth.value;
  const lastDay = new Date(year, month, 0).toISOString().slice(0, 10);
  return today < lastDay ? today : lastDay;
});

const periodRealBalances = computed(() => {
  const asOf = periodAsOf.value;
  const result: Record<string, number> = {};
  for (const account of accounts.value) {
    const delta = transactions.value
      .filter(tx => tx.account_id === account.id && tx.date <= asOf)
      .reduce((sum, tx) => sum + (tx.type === 'income' ? tx.amount : -tx.amount), 0);
    result[account.id] = account.balance + delta;
  }
  return result;
});

const barLabels   = computed(() => accounts.value.map(a => a.label));
const barDatasets = computed(() => [{
  label: t('accounts.balance'),
  data:  accounts.value.map(a => (periodRealBalances.value[a.id] ?? 0) / (CURRENCY_SUBUNIT[a.currency] ?? 100)),
  backgroundColor: accounts.value.map(a => a.color + '99'),
  borderColor:     accounts.value.map(a => a.color),
  borderRadius: 8, borderWidth: 1.5, maxBarThickness: 56,
}]);

// ── Pie chart (budget dépensé sur la période) ──────────────────────────────
const pieLabels = computed(() => budgets.value.map(b => b.label));
const pieData   = computed(() => budgets.value.map(b => (periodBudgetSpent.value[b.id] ?? 0) / (CURRENCY_SUBUNIT[b.currency as Currency] ?? 100)));
const pieColors = computed(() => budgets.value.map(b => b.color));

// ── Line chart (flux cumulé de la période) ─────────────────────────────────
const lineChart = computed(() => {
  const prefix = selectedMonthPrefix.value;
  const dailyMap: Record<string, number> = {};

  transactions.value.forEach(tx => {
    if (!tx.date.startsWith(prefix)) return;
    if (tx.date > today) return;
    if (tx.type === 'income' && tx.transfer_peer_id !== undefined) return;
    const currency = accountOf(tx.account_id)?.currency ?? 'EUR';
    const value    = tx.amount / (CURRENCY_SUBUNIT[currency] ?? 100);
    dailyMap[tx.date] = (dailyMap[tx.date] ?? 0) + (tx.type === 'income' ? value : -value);
  });

  const dates = Object.keys(dailyMap).sort();
  let cumul = 0;
  const data = dates.map(date => { cumul += dailyMap[date]; return cumul; });

  return {
    labels: dates.map(d => d.slice(8, 10)),
    datasets: [{
      label: t('dashboard.cumulativeBalance'),
      data,
      borderColor: '#6366F1',
      backgroundColor: 'rgba(99,102,241,0.15)',
      fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#6366F1',
    }],
  };
});

const lineLabels   = computed(() => lineChart.value.labels);
const lineDatasets = computed(() => lineChart.value.datasets);
</script>

<template>
  <TnsPage>
    <div class="tns-page">

        <TnsLargeTitle :title="t('dashboard.title')" />

        <!-- Hero : solde + période dans une seule card -->
        <div class="tns-hero">
          <div class="tns-hero-label-row">
            <span class="tns-hero-label">{{ t('dashboard.totalBalance') }}</span>
            <button class="tns-help-btn" @click="openHelp('hero')">
              <ion-icon :icon="helpCircleOutline" />
            </button>
          </div>
          <template v-if="totalBalance.length">
            <div v-for="entry in totalBalance" :key="entry.currency" class="tns-hero-amount">
              {{ fmt(entry.total, entry.currency) }}
            </div>
          </template>
          <div v-else class="tns-hero-empty">{{ t('accounts.empty') }}</div>

          <div class="tns-hero-sep" />

          <div class="tns-period-nav">
            <button class="tns-nav-btn" @click="prevMonth">
              <ion-icon :icon="chevronBackOutline" />
            </button>
            <span
              class="tns-period-text"
              :class="{ 'tns-period-text--current': isCurrentMonth, 'tns-period-text--past': !isCurrentMonth }"
              @click="goToCurrentMonth"
            >{{ periodLabel }}</span>
            <button class="tns-nav-btn" :class="{ 'tns-nav-btn--disabled': isCurrentMonth }" @click="nextMonth">
              <ion-icon :icon="chevronForwardOutline" />
            </button>
          </div>
        </div>

        <!-- KPIs -->
        <div class="tns-section">
          <div class="tns-section-header-row">
            <ion-icon :icon="fileTrayFullOutline" />
            <TnsSectionTitle :title="t('dashboard.monthlyOverview')" />
            <button class="tns-help-btn" @click="openHelp('kpi')">
              <ion-icon :icon="helpCircleOutline" />
            </button>
          </div>
          <div class="tns-kpi-grid">
            <TnsKpiCard :label="t('transactions.income')" :value="fmtShort(periodIncome, 'EUR')" tone="green">
              <template #icon><ion-icon :icon="trendingUpOutline" /></template>
            </TnsKpiCard>
            <TnsKpiCard :label="t('transactions.expense')" :value="fmtShort(periodExpense, 'EUR')" tone="red">
              <template #icon><ion-icon :icon="trendingDownOutline" /></template>
            </TnsKpiCard>
            <TnsKpiCard :label="t('dashboard.upcoming')" :value="fmtShort(periodUpcoming, 'EUR')" tone="orange">
              <template #icon><ion-icon :icon="timeOutline" /></template>
            </TnsKpiCard>
          </div>
        </div>

        <!-- Bar + Pie côte à côte -->
        <div class="tns-charts-row">
          <div class="tns-chart-section">
            <div class="tns-section-header-row">
              <ion-icon :icon="barChartOutline"/>
              <TnsSectionTitle :title="t('dashboard.balanceByAccount')" />
              <button class="tns-help-btn" @click="openHelp('balanceByAccount')">
                <ion-icon :icon="helpCircleOutline" />
              </button>
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
              <button class="tns-help-btn" @click="openHelp('expenseBreakdown')">
                <ion-icon :icon="helpCircleOutline" />
              </button>
            </div>
            <div class="tns-chart-card">
              <PieChart v-if="budgets.length" :labels="pieLabels" :data="pieData" :colors="pieColors" :height="200" />
              <div v-else class="tns-chart-empty">{{ t('budgets.empty') }}</div>
            </div>
          </div>
        </div>

        <!-- Line chart -->
        <div class="tns-chart-section">
          <div class="tns-section-header-row">
            <ion-icon :icon="calendarClearOutline"/>
            <TnsSectionTitle :title="t('dashboard.monthlyFlow')" />
            <button class="tns-help-btn" @click="openHelp('monthlyFlow')">
              <ion-icon :icon="helpCircleOutline" />
            </button>
          </div>
          <div class="tns-chart-card">
            <LineChart v-if="lineLabels.length" :labels="lineLabels" :datasets="lineDatasets" y-tick-suffix=" €" />
            <div v-else class="tns-chart-empty">{{ t('transactions.empty') }}</div>
          </div>
        </div>

        <!-- Budgets -->
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
                <TnsBudgetProgress
                  :spent="periodBudgetSpent[b.id] ?? 0"
                  :amount="b.amount"
                  :color="b.color"
                  :currency="b.currency"
                />
              </div>
            </div>
          </template>
          <div v-else class="tns-list-empty">{{ t('budgets.empty') }}</div>
        </TnsList>

        <!-- Transactions récentes -->
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
              :icon-color="tx.transfer_peer_id !== undefined ? 'var(--tns-accent)' : tx.color || budgetOf(tx.budget_id)?.color || '#6B7280'"
              :account-label="accountOf(tx.account_id)?.label || ''"
              :to-account-label="tx.to_account_id ? accountOf(tx.to_account_id)?.label || '' : ''"
              :show-date="true"
              :future="tx.date > today"
            >
              <template #icon>
                <ion-icon :icon="iconFor(tx)" />
              </template>
            </TnsTransactionRow>
          </template>
          <div v-else class="tns-list-empty">{{ t('transactions.empty') }}</div>
        </TnsList>

      </div>
      <TnsInfoModal
        v-if="helpTopic"
        :model-value="showHelp"
        :title="helpTopic.title"
        :body="helpTopic.body"
        @update:model-value="helpTopic = null"
      />
  </TnsPage>
</template>

<style scoped>
/* ── Hero ─────────────────────────────────────────────────────────────────── */
.tns-hero {
  margin: 0 16px 24px;
  background: var(--tns-card);
  border-radius: var(--tns-radius-xl);
  padding: 16px 20px;
  font-family: var(--tns-font);
}
.tns-hero-label-row {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-bottom: 4px;
}
.tns-hero-label {
  font-size: 12px;
  font-weight: 500;
  color: var(--tns-fg2);
}
.tns-help-btn {
  background: none;
  border: none;
  padding: 0;
  cursor: pointer;
  color: var(--tns-fg3);
  display: flex;
  align-items: center;
  font-size: 15px;
  line-height: 1;
}
.tns-help-btn:active { color: var(--tns-accent); }
.tns-hero-amount {
  font-size: 26px;
  font-weight: 700;
  color: var(--tns-accent);
  letter-spacing: -0.8px;
  font-variant-numeric: tabular-nums;
}
.tns-hero-empty {
  font-size: 18px;
  font-weight: 600;
  color: var(--tns-fg3);
}
.tns-hero-sep {
  height: 0.5px;
  background: var(--tns-sep);
  margin: 12px 0;
}
.tns-period-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.tns-nav-btn {
  background: none;
  border: none;
  padding: 4px 6px;
  cursor: pointer;
  color: var(--tns-accent);
  display: flex;
  align-items: center;
  font-size: 18px;
  border-radius: var(--tns-radius-sm);
  flex-shrink: 0;
}
.tns-nav-btn:active { background: var(--tns-sep); }
.tns-nav-btn--disabled { opacity: 0.25; pointer-events: none; }

.tns-period-text {
  font-size: 14px;
  font-weight: 600;
  text-align: center;
  flex: 1;
  cursor: pointer;
  transition: color 0.15s;
}
.tns-period-text--current { color: var(--tns-accent); }
.tns-period-text--past    { color: var(--tns-fg2); }

/* ── KPI grid ──────────────────────────────────────────────────────────────── */
.tns-kpi-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: var(--tns-gap);
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
.tns-charts-row .tns-chart-section { margin: 0; flex: 1; display: flex; flex-direction: column; }
.tns-charts-row .tns-chart-card { flex: 1; }

@media (min-width: 768px) {
  .tns-charts-row { flex-direction: row; }
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
.tns-chart-empty {
  display: flex; align-items: center; justify-content: center;
  min-height: 168px; font-family: var(--tns-font);
  font-size: 14px; color: var(--tns-fg3);
}
.tns-list-empty {
  display: flex; align-items: center; justify-content: center;
  min-height: 80px; font-family: var(--tns-font);
  font-size: 14px; color: var(--tns-fg3);
}
</style>
