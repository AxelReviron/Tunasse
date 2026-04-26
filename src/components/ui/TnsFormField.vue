<template>
  <!-- TnsFormField — grouped-list-style field for bottom-sheet forms.
       Slot default receives the input; label sits above in small caps. -->
  <div class="tns-field">
    <div class="tns-field-label">{{ label }}</div>
    <slot/>
  </div>
</template>

<script setup>
defineProps({ label: { type: String, required: true } });
</script>

<style scoped>
.tns-field {
  background: var(--tns-bg);
  border-radius: var(--tns-radius-md);
  padding: 12px 14px; margin-bottom: 10px;
  font-family: var(--tns-font);
}
.tns-field-label {
  font-size: 11.5px; color: var(--tns-fg2);
  font-weight: 600; text-transform: uppercase; letter-spacing: 0.2px;
}
/* Style any raw <input> / <textarea> dropped into the slot. */
.tns-field :deep(input),
.tns-field :deep(textarea) {
  border: none; outline: none; background: transparent;
  font-size: 17px; color: var(--tns-fg);
  width: 100%; padding: 4px 0 0;
  font-family: inherit;
  appearance: none; -webkit-appearance: none;
}
.tns-field :deep(select) {
  border: none; outline: none; background: transparent;
  font-size: 17px; color: var(--tns-fg);
  width: 100%; padding: 4px 0 0;
  font-family: inherit;
  appearance: none; -webkit-appearance: none;
}
.tns-field :deep(option) {
  background: var(--tns-card);
  color: var(--tns-fg);
}
.tns-field :deep(input[type="checkbox"]) {
  width: auto;
  accent-color: var(--tns-accent);
}
</style>
