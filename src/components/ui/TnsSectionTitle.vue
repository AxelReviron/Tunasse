<script setup lang="ts">
defineProps({ title: { type: String, required: true } });
</script>

<template>
  <div class="tns-section-title">{{ title }}</div>
</template>

<style scoped>
.tns-section-title {
  font-family: var(--tns-font);
  font-size: 17px;
  font-weight: 600;
  color: var(--tns-fg);
  padding: 4px 16px 10px;
  letter-spacing: -0.2px;
}
</style>
