<template>
  <!-- TnsSectionHeader — uppercase subtitle used above grouped lists.
       Slot "action" for an optional right-aligned link (e.g. "See all"). -->
  <div class="tns-section-hd">
    <span>{{ label }}</span>
    <span v-if="$slots.action" class="tns-section-action"><slot name="action"/></span>
  </div>
</template>

<script setup>
defineProps({ label: { type: String, required: true } });
</script>

<style scoped>
.tns-section-hd {
  display: flex; justify-content: space-between; align-items: center;
  font-size: 12.5px; font-weight: 600; color: var(--tns-fg2);
  text-transform: uppercase; letter-spacing: 0.2px;
  padding: 4px 20px 6px;
  font-family: var(--tns-font);
}
.tns-section-action {
  color: var(--tns-accent); font-weight: 500; text-transform: none;
  letter-spacing: 0; cursor: pointer;
}
</style>
