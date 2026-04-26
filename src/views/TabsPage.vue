<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRoute, useRouter } from 'vue-router';
import { IonTabBar, IonTabButton, IonTabs, IonLabel, IonIcon, IonPage, IonRouterOutlet } from '@ionic/vue';
import {home, reader, wallet, pieChart, cardOutline} from 'ionicons/icons';

const { t } = useI18n();
const route  = useRoute();
const router = useRouter();

const navItems = computed(() => [
  { route: '/tabs/dashboard',    label: t('nav.dashboard'),    icon: home },
  { route: '/tabs/transactions', label: t('nav.transactions'), icon: reader },
  { route: '/tabs/budget',       label: t('nav.budgets'),      icon: pieChart },
  { route: '/tabs/account',      label: t('nav.accounts'),     icon: cardOutline },
]);

function isActive(path: string) {
  return route.path.startsWith(path);
}
</script>

<template>
  <ion-page>
    <div class="tns-shell">

      <!-- Sidebar — desktop uniquement -->
      <aside class="tns-sidebar">
        <div class="tns-sidebar-logo">
          <ion-icon :icon="wallet" />
          <span>Tunasse</span>
        </div>
        <nav class="tns-sidebar-nav">
          <div
            v-for="item in navItems"
            :key="item.route"
            :class="['tns-nav-item', { 'tns-nav-item--active': isActive(item.route) }]"
            @click="router.push(item.route)"
          >
            <ion-icon :icon="item.icon" />
            <span>{{ item.label }}</span>
          </div>
        </nav>
      </aside>

      <!-- Contenu principal -->
      <div class="tns-main">
        <ion-tabs>
          <ion-router-outlet />
          <ion-tab-bar id="tns-tab-bar" slot="bottom" class="tns-tab-bar">
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
              <ion-icon aria-hidden="true" :icon="cardOutline" />
              <ion-label>{{ t('nav.accounts') }}</ion-label>
            </ion-tab-button>
          </ion-tab-bar>
        </ion-tabs>
      </div>

    </div>
  </ion-page>
</template>

<style scoped>
/* ── Shell ───────────────────────────────────────────────────────────────── */
.tns-shell {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
}

/* ── Sidebar cachée sur mobile ───────────────────────────────────────────── */
.tns-sidebar {
  display: none;
}

/* ── Main : occupe tout l'espace restant ─────────────────────────────────── */
.tns-main {
  flex: 1;
  min-height: 0;
  min-width: 0;
  position: relative;
  overflow: hidden;
}

/* ── Desktop ≥ 768px ─────────────────────────────────────────────────────── */
@media (min-width: 768px) {
  .tns-shell {
    flex-direction: row;
  }

  .tns-sidebar {
    display: flex;
    flex-direction: column;
    width: 220px;
    flex-shrink: 0;
    height: 100%;
    background: var(--tns-card);
    border-right: 0.5px solid var(--tns-sep);
    padding: 28px 12px 24px;
    overflow-y: auto;
  }

  /* Cache la tab bar mobile sur desktop */
  .tns-tab-bar {
    display: none !important;
  }
}

/* ── Sidebar logo ────────────────────────────────────────────────────────── */
.tns-sidebar-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 8px 24px;
  margin-bottom: 8px;
  border-bottom: 0.5px solid var(--tns-sep);
  font-family: var(--tns-font);
  font-size: 17px;
  font-weight: 700;
  color: var(--tns-fg);
}

.tns-sidebar-logo ion-icon {
  font-size: 22px;
  color: var(--tns-accent);
}

/* ── Nav items ───────────────────────────────────────────────────────────── */
.tns-sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 2px;
  margin-top: 8px;
}

.tns-nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: var(--tns-radius-md);
  font-family: var(--tns-font);
  font-size: 15px;
  font-weight: 500;
  color: var(--tns-fg2);
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
  user-select: none;
}

.tns-nav-item ion-icon {
  font-size: 18px;
  flex-shrink: 0;
}

.tns-nav-item:hover {
  background: rgba(255, 255, 255, 0.05);
  color: var(--tns-fg);
}

.tns-nav-item--active {
  background: rgba(99, 102, 241, 0.15);
  color: var(--tns-accent);
}
</style>
