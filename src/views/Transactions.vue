<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { IonPage, IonContent, IonIcon, toastController } from '@ionic/vue';
import {
  cartOutline, arrowDownOutline,
  repeatOutline, addOutline, funnelOutline, swapHorizontalOutline,
} from 'ionicons/icons';

import TnsLargeTitle          from '@/components/ui/TnsLargeTitle.vue';
import TnsSectionHeader       from '@/components/ui/TnsSectionHeader.vue';
import TnsList                from '@/components/ui/TnsList.vue';
import TnsTransactionRow      from '@/components/ui/TnsTransactionRow.vue';
import TnsTransactionSheet    from '@/components/TnsTransactionSheet.vue';
import TnsFiltersSheet        from '@/components/TnsFiltersSheet.vue';
import { useAccounts }        from '@/composables/useAccounts';
import { useBudgets }         from '@/composables/useBudgets';
import { useTransactions }    from '@/composables/useTransactions';
import { useTransactionFilters } from '@/composables/useTransactionFilters';
import { ICON_MAP }           from '@/constants/icons';
import type { Transaction }   from '@/types';

const { t } = useI18n();

const { accounts, getById: accountOf } = useAccounts();
const { getById: budgetOf }  = useBudgets();
const { transactions }       = useTransactions();

const { filter, dateRange, query, grouped } = useTransactionFilters(transactions);

const showSheet      = ref(false);
const showFilters    = ref(false);
const selectedTx     = ref<Transaction | undefined>(undefined);

async function showNoAccountToast() {
  const toast = await toastController.create({
    message:        t('transactions.noAccount'),
    duration:       3000,
    position:       'bottom',
    positionAnchor: 'tns-tab-bar',
    cssClass:       'tns-toast',
  })
  await toast.present()
}

const activeFilterCount = computed(() =>
  (filter.value !== 'all' ? 1 : 0) + (dateRange.value !== 'all' ? 1 : 0)
)

function resetFilters() {
  filter.value    = 'all'
  dateRange.value = 'all'
  showFilters.value = false
}

function openCreate() {
  if (!accounts.value.length) {
    showNoAccountToast();
    return;
  }
  selectedTx.value = undefined;
  showSheet.value  = true;
}

function openEdit(tx: Transaction) {
  selectedTx.value = tx;
  showSheet.value  = true;
}

function onSheetClose() {
  showSheet.value  = false;
  selectedTx.value = undefined;
}

function iconFor(tx: { type: string; icon?: string; is_recurring?: boolean; transfer_peer_id?: string }) {
  if (tx.transfer_peer_id !== undefined) return swapHorizontalOutline;
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

        <div class="tns-filters">
          <div class="tns-search">
            <span class="tns-search-icon">⌕</span>
            <input
              v-model="query"
              type="search"
              :placeholder="t('transactions.search')"
              class="tns-search-input"
            />
            <button class="tns-filter-btn" :class="{ active: activeFilterCount > 0 }" @click="showFilters = true">
              <ion-icon :icon="funnelOutline" />
              <span class="tns-filter-btn-label">{{ t('common.filters') }}</span>
              <span v-if="activeFilterCount" class="tns-filter-badge">{{ activeFilterCount }}</span>
            </button>
          </div>
        </div>

        <template v-if="Object.keys(grouped).length">
          <template v-for="(txs, day) in grouped" :key="day">
            <TnsSectionHeader :label="day" />
            <TnsList>
              <TnsTransactionRow
                v-for="tx in txs"
                :key="tx.id"
                :transaction="tx"
                :currency="accountOf(tx.account_id)?.currency || 'EUR'"
                :icon-color="tx.transfer_peer_id !== undefined ? 'var(--tns-accent)' : tx.color || budgetOf(tx.budget_id)?.color || '#6B7280'"
                :account-label="accountOf(tx.account_id)?.label || ''"
                :to-account-label="tx.to_account_id ? accountOf(tx.to_account_id)?.label || '' : ''"
                :show-date="false"
                @click="openEdit(tx)"
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

      <button class="tns-fab" @click="openCreate">
        <ion-icon :icon="addOutline" />
      </button>

    </ion-content>

    <TnsTransactionSheet
      v-model="showSheet"
      :transaction="selectedTx"
      @saved="onSheetClose"
      @deleted="onSheetClose"
    />


<TnsFiltersSheet
      v-model="showFilters"
      v-model:filter="filter"
      v-model:date-range="dateRange"
      @reset="resetFilters"
    />
  </ion-page>
</template>

<style>
.tns-toast {
  --background: var(--tns-accent);
  --color: #fff;
  --border-radius: var(--tns-radius-lg);
  --box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
}
</style>

<style scoped>
.tns-empty {
  text-align: center;
  padding: 48px 16px;
  color: var(--tns-fg2);
  font-family: var(--tns-font);
  font-size: 15px;
}

.tns-filters {
  margin: 0 16px 12px;
  background: var(--tns-card);
  border-radius: var(--tns-radius-xl);
  padding: 6px 4px;
}

.tns-search {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 14px;
}

.tns-search-icon {
  font-size: 28px;
  color: var(--tns-fg3);
  flex-shrink: 0;
  line-height: 1;
}

.tns-search-input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  font-size: 15px;
  color: var(--tns-fg);
  font-family: var(--tns-font);
}

.tns-search-input::placeholder { color: var(--tns-fg3); }
.tns-search-input::-webkit-search-cancel-button { cursor: pointer; }

.tns-filter-btn {
  position: relative;
  display: flex;
  align-items: center;
  gap: 4px;
  flex-shrink: 0;
  background: rgba(120, 120, 128, 0.14);
  border: none;
  border-radius: 8px;
  color: var(--tns-fg2);
  font-size: 13px;
  font-weight: 500;
  font-family: var(--tns-font);
  padding: 6px 10px;
  cursor: pointer;
  white-space: nowrap;
}
.tns-filter-btn ion-icon { font-size: 14px; }
.tns-filter-btn.active { background: var(--tns-accent); color: #fff; }

.tns-filter-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: var(--tns-accent);
  color: #fff;
  font-size: 9px;
  font-weight: 700;
  line-height: 14px;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  text-align: center;
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
