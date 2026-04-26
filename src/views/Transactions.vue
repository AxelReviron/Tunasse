<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { IonPage, IonContent, IonIcon } from '@ionic/vue';
import {
  cartOutline, restaurantOutline, arrowDownOutline,
  repeatOutline, homeOutline, carOutline, addOutline,
} from 'ionicons/icons';

import TnsLargeTitle          from '@/components/ui/TnsLargeTitle.vue';
import TnsFilterChips         from '@/components/ui/TnsFilterChips.vue';
import TnsSectionHeader       from '@/components/ui/TnsSectionHeader.vue';
import TnsList                from '@/components/ui/TnsList.vue';
import TnsTransactionRow      from '@/components/ui/TnsTransactionRow.vue';
import TnsTransactionSheet    from '@/components/TnsTransactionSheet.vue';
import { useAccounts }        from '@/composables/useAccounts';
import { useBudgets }         from '@/composables/useBudgets';
import { useTransactions }    from '@/composables/useTransactions';
import { useTransactionFilters } from '@/composables/useTransactionFilters';
import { ICON_MAP }           from '@/constants/icons';

const { t } = useI18n();

const { accounts, getById: accountOf } = useAccounts();
const { getById: budgetOf }            = useBudgets();
const { transactions }                 = useTransactions();

const { filter, grouped } = useTransactionFilters(transactions);

const showSheet = ref(false);

function iconFor(tx: { type: string; icon?: string; is_recurring?: boolean }) {
  if (tx.icon && ICON_MAP[tx.icon]) return ICON_MAP[tx.icon];
  if (tx.type === 'income') return arrowDownOutline;
  if (tx.is_recurring)      return repeatOutline;
  return cartOutline;
}
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" :style="{ '--background': 'var(--tns-bg)' }">
      <div class="tns-page">

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
                :icon-color="tx.color || budgetOf(tx.budget_id)?.color || '#6B7280'"
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

      </div>

      <button class="tns-fab" @click="showSheet = true">
        <ion-icon :icon="addOutline"  />
      </button>

    </ion-content>

    <TnsTransactionSheet v-model="showSheet" @saved="showSheet = false" />
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
