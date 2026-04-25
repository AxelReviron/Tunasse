<template>
  <ion-page>
    <ion-tabs>
      <ion-router-outlet></ion-router-outlet>
      <ion-tab-bar slot="bottom">

        <ion-tab-button tab="dashboard" href="/tabs/dashboard">
          <ion-icon aria-hidden="true" :icon="home" />
          <ion-label>Dashboard</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="transactions" href="/tabs/transactions">
          <ion-icon aria-hidden="true" :icon="reader" />
          <ion-label>Transactions</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="budget" href="/tabs/budget">
          <ion-icon aria-hidden="true" :icon="pieChart" />
          <ion-label>Budget</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="account" href="/tabs/account">
          <ion-icon aria-hidden="true" :icon="wallet" />
          <ion-label>Account</ion-label>
        </ion-tab-button>

      </ion-tab-bar>
    </ion-tabs>
  </ion-page>
</template>

<script setup lang="ts">
import { IonTabBar, IonTabButton, IonTabs, IonLabel, IonIcon, IonPage, IonRouterOutlet } from '@ionic/vue';
import { home, reader, wallet, pieChart } from 'ionicons/icons';
</script>
