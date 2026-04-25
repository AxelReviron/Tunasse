<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import type { Account, Budget, Transaction } from '@/types';
import { IonPage, IonContent, IonIcon } from '@ionic/vue';
import {
  cartOutline, restaurantOutline, arrowDownOutline,
  repeatOutline, homeOutline, carOutline,
} from 'ionicons/icons';

import TnsLargeTitle       from '@/components/ui/TnsLargeTitle.vue';
import TnsFilterChips      from '@/components/ui/TnsFilterChips.vue';
import TnsSectionHeader    from '@/components/ui/TnsSectionHeader.vue';
import TnsList             from '@/components/ui/TnsList.vue';
import TnsTransactionRow   from '@/components/ui/TnsTransactionRow.vue';
import { useTransactionFilters } from '@/composables/useTransactionFilters';

const { t } = useI18n();

// ─── Mock data ────────────────────────────────────────────────────────────────
const accounts = ref<Account[]>([
  { id: 1, label: 'Compte courant', currency: 'EUR', iban: '****1234', type: 'checking', balance: 3200, color: '#4F46E5' },
  { id: 2, label: 'Livret A',       currency: 'EUR', iban: '****5678', type: 'savings',  balance: 8000, color: '#16A34A' },
]);

const budgets = ref<Budget[]>([
  { id: 1, label: 'Courses',    color: '#EA580C', amount: 400, spent: 230, currency: 'EUR' },
  { id: 2, label: 'Restaurant', color: '#DC2626', amount: 150, spent: 90,  currency: 'EUR' },
  { id: 3, label: 'Logement',   color: '#4F46E5', amount: 900, spent: 900, currency: 'EUR' },
  { id: 4, label: 'Transport',  color: '#0EA5E9', amount: 100, spent: 60,  currency: 'EUR' },
]);

const transactions = ref<Transaction[]>([
  { id: 1, label: 'Carrefour',        amount: 54.30, type: 'expense', date: '2026-04-23', account_id: 1, budget_id: 1, category: 'Courses' },
  { id: 2, label: 'Salaire avril',    amount: 2800,  type: 'income',  date: '2026-04-23', account_id: 1 },
  { id: 3, label: 'Sushi Shop',       amount: 32.00, type: 'expense', date: '2026-04-22', account_id: 1, budget_id: 2, category: 'Restaurant' },
  { id: 4, label: 'Loyer',            amount: 900,   type: 'expense', date: '2026-04-22', account_id: 1, budget_id: 3, category: 'Logement', is_recurring: true, recurring_unit: 'month' },
  { id: 5, label: 'Lidl',             amount: 38.10, type: 'expense', date: '2026-04-21', account_id: 1, budget_id: 1, category: 'Courses' },
  { id: 6, label: 'Navigo mensuel',   amount: 86.40, type: 'expense', date: '2026-04-20', account_id: 1, budget_id: 4, category: 'Transport', is_recurring: true, recurring_unit: 'month' },
  { id: 7, label: 'Virement épargne', amount: 200,   type: 'expense', date: '2026-04-20', account_id: 1 },
]);
// ─────────────────────────────────────────────────────────────────────────────

const { filter, grouped } = useTransactionFilters(transactions);

function accountOf(id: number) { return accounts.value.find(a => a.id === id); }
function budgetOf(id?: number) { return id ? budgets.value.find(b => b.id === id) : undefined; }

const ICON_MAP: Record<string, unknown> = {
  Courses:    cartOutline,
  Restaurant: restaurantOutline,
  Logement:   homeOutline,
  Transport:  carOutline,
};

function iconFor(tx: { type: string; category?: string; is_recurring?: boolean }) {
  if (tx.type === 'income') return arrowDownOutline;
  if (tx.is_recurring)      return repeatOutline;
  return ICON_MAP[tx.category ?? ''] ?? cartOutline;
}
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" :style="{ '--background': 'var(--tns-bg)' }">
      <TnsLargeTitle :title="t('transactions.title')" />

      <TnsFilterChips
        v-model="filter"
        :chips="[
          { value: 'all',       label: t('transactions.all') },
          { value: 'income',    label: t('transactions.income') },
          { value: 'expense',   label: t('transactions.expense') },
          { value: 'recurring', label: t('transactions.recurring') },
        ]"
      />

      <template v-if="Object.keys(grouped).length">
        <template v-for="(txs, day) in grouped" :key="day">
          <TnsSectionHeader :label="day" />
          <TnsList>
            <TnsTransactionRow
              v-for="tx in txs"
              :key="tx.id"
              :transaction="tx"
              :currency="accountOf(tx.account_id)?.currency || 'EUR'"
              :icon-color="budgetOf(tx.budget_id)?.color || '#6B7280'"
              :account-label="accountOf(tx.account_id)?.label || ''"
              :show-date="false"
            >
              <template #icon>
                <ion-icon :icon="iconFor(tx)" />
              </template>
            </TnsTransactionRow>
          </TnsList>
        </template>
      </template>

      <div v-else class="tns-empty">{{ t('transactions.empty') }}</div>
    </ion-content>
  </ion-page>
</template>

<style scoped>
.tns-empty {
  text-align: center;
  padding: 48px 16px;
  color: var(--tns-fg2);
  font-family: var(--tns-font);
  font-size: 15px;
}
</style>
