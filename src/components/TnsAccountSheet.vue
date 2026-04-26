<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { IonIcon } from '@ionic/vue'
import { closeOutline } from 'ionicons/icons'

import TnsSheet      from '@/components/ui/TnsSheet.vue'
import TnsFormField  from '@/components/ui/TnsFormField.vue'
import TnsColorPicker from '@/components/ui/TnsColorPicker.vue'

import { useAccounts } from '@/composables/useAccounts'
import { DEFAULT_COLOR } from '@/constants/colors'
import { CURRENCY_SUBUNIT, toSubunits, fromSubunits } from '@/constants/currencies'
import type { Account, AccountType, Currency } from '@/types'

const props = defineProps<{
  modelValue: boolean
  account?:   Account
}>()

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  'saved':   []
  'deleted': []
}>()

const { t } = useI18n()
const { create, update, remove } = useAccounts()

const label    = ref('')
const type     = ref<AccountType>('checking')
const currency = ref<Currency>('EUR')
const color    = ref(DEFAULT_COLOR)
const balance  = ref('0')

const isEditMode = computed(() => !!props.account)

watch(() => props.modelValue, open => {
  if (!open) return
  if (props.account) fill(props.account)
  else reset()
})

function fill(a: Account) {
  label.value    = a.label
  type.value     = a.type
  currency.value = a.currency
  color.value    = a.color
  balance.value  = fromSubunits(a.balance, CURRENCY_SUBUNIT[a.currency])
}

function reset() {
  label.value    = ''
  type.value     = 'checking'
  currency.value = 'EUR'
  color.value    = DEFAULT_COLOR
  balance.value  = '0'
}

const canSave = computed(() => label.value.trim() !== '')

function close() {
  emit('update:modelValue', false)
  reset()
}

async function save() {
  if (!canSave.value) return
  const payload = {
    label:    label.value.trim(),
    type:     type.value,
    currency: currency.value,
    color:    color.value,
    balance:  toSubunits(balance.value || '0', CURRENCY_SUBUNIT[currency.value]),
  }
  if (isEditMode.value && props.account) {
    await update(props.account.id, payload)
  } else {
    await create(payload)
  }
  emit('saved')
  close()
}

async function deleteAccount() {
  if (!props.account) return
  await remove(props.account.id)
  emit('deleted')
  close()
}
</script>

<template>
  <TnsSheet
    :model-value="modelValue"
    :title="isEditMode ? t('accounts.edit') : t('accounts.new')"
    @update:model-value="close"
  >
    <template #closeIcon>
      <ion-icon :icon="closeOutline" style="font-size:20px" />
    </template>

    <TnsFormField :label="t('accounts.label')">
      <input v-model="label" type="text" :placeholder="t('accounts.label')" />
    </TnsFormField>

    <TnsFormField :label="t('accounts.type.label')">
      <select v-model="type">
        <option value="checking">{{ t('accounts.type.checking') }}</option>
        <option value="savings">{{ t('accounts.type.savings') }}</option>
        <option value="investment">{{ t('accounts.type.investment') }}</option>
      </select>
    </TnsFormField>

    <TnsFormField :label="t('accounts.currency')">
      <select v-model="currency">
        <option value="EUR">EUR — Euro</option>
        <option value="USD">USD — Dollar</option>
        <option value="GBP">GBP — Livre sterling</option>
        <option value="BTC">BTC — Bitcoin</option>
      </select>
    </TnsFormField>

    <TnsFormField :label="t('accounts.initialBalance')">
      <input v-model="balance" type="number" min="0" step="0.01" />
    </TnsFormField>

    <TnsFormField :label="t('transactions.color')">
      <TnsColorPicker v-model="color" />
    </TnsFormField>

    <div class="tns-actions">
      <button v-if="isEditMode" class="tns-delete-btn" @click="deleteAccount">
        {{ t('common.delete') }}
      </button>
      <button class="tns-save-btn" :disabled="!canSave" @click="save">
        {{ t('common.save') }}
      </button>
    </div>
  </TnsSheet>
</template>

<style scoped>
.tns-actions {
  display: flex;
  gap: 10px;
  margin-top: 8px;
}
.tns-save-btn {
  flex: 1;
  padding: 16px;
  border: none;
  border-radius: var(--tns-radius-md);
  background: var(--tns-accent);
  color: #fff;
  font-size: 16px;
  font-weight: 600;
  font-family: var(--tns-font);
  cursor: pointer;
  transition: opacity .15s;
}
.tns-save-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.tns-delete-btn {
  padding: 16px 20px;
  border: 1.5px solid var(--tns-red);
  border-radius: var(--tns-radius-md);
  background: transparent;
  color: var(--tns-red);
  font-size: 16px;
  font-weight: 600;
  font-family: var(--tns-font);
  cursor: pointer;
}
</style>
