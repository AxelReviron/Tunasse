<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent } from '@ionic/vue';
import ExploreContainer from '@/components/ExploreContainer.vue';

const { t } = useI18n();
</script>

<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>{{ t('accounts.title') }}</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <ion-header collapse="condense">
        <ion-toolbar>
          <ion-title size="large">{{ t('accounts.title') }}</ion-title>
        </ion-toolbar>
      </ion-header>
      <ExploreContainer :name="t('accounts.title')" />
    </ion-content>
  </ion-page>
</template>
