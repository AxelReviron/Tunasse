<script setup>
import { IonPage, IonContent } from '@ionic/vue';
import TnsFooter from '@/components/ui/TnsFooter.vue';
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" :style="{ '--background': 'var(--tns-bg)' }">
      <slot />
      <div class="separator"></div>
      <TnsFooter />
    </ion-content>
    <slot name="modals" />
  </ion-page>
</template>

<style>
.separator {
  margin-top: 2rem;
  border-top: 0.5px solid var(--tns-sep);
}
</style>
