<script setup lang="ts">

import TnsLargeTitle from "@/components/ui/TnsLargeTitle.vue";
import {IonContent, IonIcon, IonPage} from "@ionic/vue";
import {useI18n} from "vue-i18n";
import { joinRoom } from 'trystero'
import { ref } from 'vue';
import { syncOutline } from "ionicons/icons";
import TnsSectionTitle from "@/components/ui/TnsSectionTitle.vue";

const { t } = useI18n();

// Join a room with your app ID and a room ID
const room = joinRoom(
    {appId: 'tunasse'},
    'tunasse-id', // TODO: Generate a unique room ID for each user with a passphrase
)

const devicesConnected = ref([]);
const [startSync, receiveSync] = room.makeAction('data')

receiveSync((data, peerId) => {
  console.log(`reçu de ${peerId}:`, data)
})

// Handle when a peer joins
room.onPeerJoin(peerId => {
  console.log(`${peerId} joined!`)

  devicesConnected.value.push(peerId)

  // startSync('test', peerId)

})

</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" :style="{ '--background': 'var(--tns-bg)' }">
      <div class="tns-page">
        <TnsLargeTitle :title="t('settings.title')" />

        <div class="tns-section">
          <div class="tns-section-header-row">
            <ion-icon :icon="syncOutline" />
            <TnsSectionTitle :title="t('settings.sync')" />
          </div>
        </div>

      </div>
    </ion-content>
  </ion-page>
</template>

<style scoped>

</style>