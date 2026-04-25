<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { IonTabBar, IonTabButton, IonTabs, IonLabel, IonIcon, IonPage, IonRouterOutlet } from '@ionic/vue';
import { home, reader, wallet, pieChart } from 'ionicons/icons';

const { t } = useI18n();
</script>

<template>
  <ion-page>
    <ion-tabs>
      <ion-router-outlet></ion-router-outlet>
      <ion-tab-bar slot="bottom">

        <ion-tab-button tab="dashboard" href="/tabs/dashboard">
          <ion-icon aria-hidden="true" :icon="home" />
          <ion-label>{{ t('nav.dashboard') }}</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="transactions" href="/tabs/transactions">
          <ion-icon aria-hidden="true" :icon="reader" />
          <ion-label>{{ t('nav.transactions') }}</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="budget" href="/tabs/budget">
          <ion-icon aria-hidden="true" :icon="pieChart" />
          <ion-label>{{ t('nav.budgets') }}</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="account" href="/tabs/account">
          <ion-icon aria-hidden="true" :icon="wallet" />
          <ion-label>{{ t('nav.accounts') }}</ion-label>
        </ion-tab-button>

      </ion-tab-bar>
    </ion-tabs>
  </ion-page>
</template>
