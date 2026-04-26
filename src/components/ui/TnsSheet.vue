<template>
  <!-- TnsBottomSheet — iOS-style modal sheet with handle, title,
       close button, scrollable body, sticky primary button. -->
  <Transition name="tns-sheet">
    <div v-if="modelValue" class="tns-sheet-bg" @click.self="$emit('update:modelValue', false)">
      <div class="tns-sheet">
        <div class="tns-sheet-handle"/>
        <div class="tns-sheet-head">
          <div style="width:60px"/>
          <div class="tns-sheet-title">{{ title }}</div>
          <button class="tns-sheet-close" @click="$emit('update:modelValue', false)">
            <slot name="closeIcon"/>
          </button>
        </div>
        <slot/>
      </div>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  modelValue: { type: Boolean, required: true },
  title:      { type: String, required: true },
});
defineEmits(['update:modelValue']);
</script>

<style scoped>
/* ── Backdrop ─────────────────────────────────────────────────────────────── */
.tns-sheet-bg {
  position: absolute; inset: 0;
  background: rgba(0, 0, 0, 0.4); z-index: 40;
  display: flex; align-items: flex-end;
}

/* ── Sheet ────────────────────────────────────────────────────────────────── */
.tns-sheet {
  width: 100%; background: var(--tns-card);
  border-radius: var(--tns-radius-xl) var(--tns-radius-xl) 0 0;
  padding: 10px 16px 28px; max-height: 85%;
  overflow-y: auto;
  font-family: var(--tns-font);
}
.tns-sheet-handle {
  width: 36px; height: 4px;
  background: rgba(120, 120, 128, 0.35); border-radius: 2px;
  margin: 4px auto 14px;
}
.tns-sheet-head {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 14px;
}
.tns-sheet-title {
  font-size: 20px; font-weight: 700; letter-spacing: -0.3px;
  color: var(--tns-fg);
}
.tns-sheet-close {
  background: none; border: none;
  color: var(--tns-fg2); width: 60px; text-align: right;
  cursor: pointer;
}

/* ── Transitions ──────────────────────────────────────────────────────────── */
.tns-sheet-enter-active,
.tns-sheet-leave-active { transition: opacity .2s ease-out; }
.tns-sheet-enter-from,
.tns-sheet-leave-to { opacity: 0; }

.tns-sheet-enter-active .tns-sheet,
.tns-sheet-leave-active .tns-sheet { transition: transform .25s ease-out; }
.tns-sheet-enter-from .tns-sheet,
.tns-sheet-leave-to   .tns-sheet { transform: translateY(100%); }
</style>
