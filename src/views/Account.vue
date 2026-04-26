<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { IonPage, IonContent, IonIcon } from '@ionic/vue';
import { cardOutline, leafOutline, barChartOutline, addOutline, chevronForwardOutline } from 'ionicons/icons';

import TnsLargeTitle   from '@/components/ui/TnsLargeTitle.vue';
import TnsList         from '@/components/ui/TnsList.vue';
import TnsAccountCard  from '@/components/ui/TnsAccountCard.vue';
import TnsAccountSheet from '@/components/TnsAccountSheet.vue';
import { useAccounts } from '@/composables/useAccounts';
import { useFormat }   from '@/composables/useFormat';
import type { Account } from '@/types';

const { t } = useI18n();
const { fmt } = useFormat();
const { accounts, totalBalance, realBalances } = useAccounts();

const showSheet  = ref(false);
const selectedAccount = ref<Account | undefined>(undefined);

function openCreate() {
  selectedAccount.value = undefined;
  showSheet.value = true;
}

function openEdit(account: Account) {
  selectedAccount.value = account;
  showSheet.value = true;
}

function onSheetClose() {
  showSheet.value = false;
  selectedAccount.value = undefined;
}

const ICON_BY_TYPE: Record<string, unknown> = {
  checking:   cardOutline,
  savings:    leafOutline,
  investment: barChartOutline,
}

function iconFor(account: Account) {
  return ICON_BY_TYPE[account.type] ?? cardOutline
}
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" :style="{ '--background': 'var(--tns-bg)' }">
      <div class="tns-page">

        <TnsLargeTitle :title="t('accounts.title')" />

        <div class="tns-balance">
          <div class="tns-balance-label">{{ t('dashboard.totalBalance') }}</div>
          <template v-if="totalBalance.length">
            <div
              v-for="entry in totalBalance"
              :key="entry.currency"
              class="tns-balance-amount"
            >{{ fmt(entry.total, entry.currency) }}</div>
          </template>
          <div v-else class="tns-balance-empty">—</div>
        </div>

        <TnsList v-if="accounts.length">
          <TnsAccountCard
            v-for="a in accounts"
            :key="a.id"
            :account="a"
            :real-balance="realBalances[a.id] ?? a.balance"
            @click="openEdit(a)"
          >
            <template #icon>
              <ion-icon :icon="iconFor(a)" />
            </template>
            <template #chevron>
              <ion-icon :icon="chevronForwardOutline" />
            </template>
          </TnsAccountCard>
        </TnsList>

        <div v-else class="tns-empty">{{ t('accounts.empty') }}</div>

      </div>

      <button class="tns-fab" @click="openCreate">
        <ion-icon :icon="addOutline" />
      </button>

    </ion-content>

    <TnsAccountSheet
      v-model="showSheet"
      :account="selectedAccount"
      @saved="onSheetClose"
      @deleted="onSheetClose"
    />
  </ion-page>
</template>

<style scoped>
.tns-page { margin-top: 16px; }

.tns-balance {
  margin: 0 16px 24px;
  background: var(--tns-card);
  border-radius: var(--tns-radius-xl);
  padding: 24px;
  font-family: var(--tns-font);
}
.tns-balance-label { font-size: 13px; font-weight: 500; color: var(--tns-fg2); }
.tns-balance-amount {
  font-size: 36px; font-weight: 700; color: var(--tns-accent);
  letter-spacing: -1px; margin-top: 4px;
  font-variant-numeric: tabular-nums;
}
.tns-balance-empty { font-size: 22px; font-weight: 600; color: var(--tns-fg3); margin-top: 4px; }

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
