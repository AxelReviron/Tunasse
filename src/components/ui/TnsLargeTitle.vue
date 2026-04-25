<template>
  <!-- TnsLargeTitle — iOS 34px "large title" used at the top of pages.
       Sits above the scrollable content, inside an IonContent.  -->
  <div class="tns-lt">
    <h1 class="tns-lt-text"><slot>{{ title }}</slot></h1>
  </div>
</template>

<script setup>
defineProps({ title: { type: String, default: '' } });
</script>

<style scoped>
.tns-lt { padding: 4px 16px 12px; }
.tns-lt-text {
  font-size: 34px; font-weight: 700; letter-spacing: 0.38px;
  color: var(--tns-fg); font-family: var(--tns-font);
  margin: 0;
}
</style>
