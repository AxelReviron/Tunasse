<script setup lang="ts">
import { IonApp, IonRouterOutlet } from '@ionic/vue';
import { joinRoom } from 'trystero'
import { ref } from 'vue';

// Join a room with your app ID and a room ID
const room = joinRoom(
    {appId: 'tunasse'},
    'tunasse-id', // TODO: Generate a unique room ID for each user with a passphrase
)

const devicesConnected = ref([]);
const [sendData, getData] = room.makeAction('data')

getData((data, peerId) => {
  console.log(`reçu de ${peerId}:`, data)
})

// Handle when a peer joins
room.onPeerJoin(peerId => {
  console.log(`${peerId} joined!`)

  devicesConnected.value.push(peerId)

  sendData('test', peerId)

})
</script>

<template>
  <ion-app>
    <ion-router-outlet />
  </ion-app>
</template>
