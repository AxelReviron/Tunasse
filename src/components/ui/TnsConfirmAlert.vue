<script setup lang="ts">
defineProps<{
  modelValue:    boolean
  title:         string
  message?:      string
  confirmLabel?: string
  cancelLabel?:  string
}>()

const emit = defineEmits<{
  'update:modelValue': [boolean]
  'confirm': []
}>()

function cancel()  { emit('update:modelValue', false) }
function confirm() { emit('confirm'); emit('update:modelValue', false) }
</script>

<template>
  <Teleport to="body">
    <Transition name="tns-confirm">
      <div v-if="modelValue" class="tns-confirm-backdrop" @click.self="cancel">
        <div class="tns-confirm-card">
          <div class="tns-confirm-body">
            <p class="tns-confirm-title">{{ title }}</p>
            <p v-if="message" class="tns-confirm-message">{{ message }}</p>
          </div>
          <div class="tns-confirm-actions">
            <button class="tns-confirm-btn tns-confirm-cancel" @click="cancel">
              {{ cancelLabel ?? 'Annuler' }}
            </button>
            <button class="tns-confirm-btn tns-confirm-destructive" @click="confirm">
              {{ confirmLabel ?? 'Supprimer' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.tns-confirm-backdrop {
  position:        fixed;
  inset:           0;
  background:      rgba(0, 0, 0, 0.65);
  display:         flex;
  align-items:     center;
  justify-content: center;
  z-index:         99999;
  padding:         24px;
  padding-left:    calc(var(--tns-sidebar-w) + 24px);
}

.tns-confirm-card {
  background:    var(--tns-card);
  border-radius: var(--tns-radius-xl);
  border:        1px solid var(--tns-sep);
  width:         100%;
  max-width:     300px;
  overflow:      hidden;
}

.tns-confirm-body {
  padding:    22px 20px 16px;
  text-align: center;
}

.tns-confirm-title {
  font-family:  var(--tns-font);
  font-size:    16px;
  font-weight:  600;
  color:        var(--tns-fg);
  margin:       0 0 8px;
  line-height:  1.3;
}

.tns-confirm-message {
  font-family:  var(--tns-font);
  font-size:    13px;
  color:        var(--tns-fg2);
  line-height:  1.5;
  margin:       0;
}

.tns-confirm-actions {
  display:    flex;
  border-top: 1px solid var(--tns-sep);
}

.tns-confirm-btn {
  flex:        1;
  padding:     14px 8px;
  border:      none;
  background:  transparent;
  font-family: var(--tns-font);
  font-size:   15px;
  cursor:      pointer;
  transition:  background 0.12s;
}

.tns-confirm-btn:active { background: rgba(255, 255, 255, 0.05); }

.tns-confirm-cancel {
  color:        var(--tns-fg2);
  border-right: 1px solid var(--tns-sep);
}

.tns-confirm-destructive {
  color:       var(--tns-red);
  font-weight: 600;
}

/* Transition */
.tns-confirm-enter-active { transition: opacity 0.18s ease; }
.tns-confirm-leave-active { transition: opacity 0.15s ease; }
.tns-confirm-enter-active .tns-confirm-card,
.tns-confirm-leave-active .tns-confirm-card { transition: transform 0.18s ease; }

.tns-confirm-enter-from,
.tns-confirm-leave-to                       { opacity: 0; }
.tns-confirm-enter-from .tns-confirm-card,
.tns-confirm-leave-to   .tns-confirm-card   { transform: scale(0.94); }
</style>
