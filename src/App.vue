<script setup lang="ts">
import { IonApp, IonRouterOutlet } from '@ionic/vue'
import TnsUpdatePrompt  from '@/components/ui/TnsUpdatePrompt.vue'
import TnsInstallPrompt from '@/components/ui/TnsInstallPrompt.vue'
</script>

<template>
  <ion-app>
    <ion-router-outlet />
    <TnsUpdatePrompt />
    <TnsInstallPrompt />
  </ion-app>
</template>
