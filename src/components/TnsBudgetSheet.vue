<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { IonIcon } from '@ionic/vue'
import { closeOutline } from 'ionicons/icons'

import TnsSheet       from '@/components/ui/TnsSheet.vue'
import TnsFormField   from '@/components/ui/TnsFormField.vue'
import TnsColorPicker from '@/components/ui/TnsColorPicker.vue'
import TnsIconPicker  from '@/components/ui/TnsIconPicker.vue'

import { useBudgets }  from '@/composables/useBudgets'
import { DEFAULT_COLOR } from '@/constants/colors'
import { DEFAULT_ICON_EXPENSE } from '@/constants/icons'
import { CURRENCY_SUBUNIT, toSubunits, fromSubunits } from '@/constants/currencies'
import type { Budget, Currency } from '@/types'

const props = defineProps<{
  modelValue: boolean
  budget?:    Budget
}>()

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  'saved':   []
  'deleted': []
}>()

const { t } = useI18n()
const { create, update, remove } = useBudgets()

const label    = ref('')
const amount   = ref('0')
const currency = ref<Currency>('EUR')
const color    = ref(DEFAULT_COLOR)
const icon     = ref(DEFAULT_ICON_EXPENSE)

const isEditMode = computed(() => !!props.budget)

watch(() => props.modelValue, open => {
  if (!open) return
  if (props.budget) fill(props.budget)
  else reset()
})

function fill(b: Budget) {
  label.value    = b.label
  amount.value   = fromSubunits(b.amount, CURRENCY_SUBUNIT[b.currency as Currency] ?? 100)
  currency.value = b.currency as Currency
  color.value    = b.color
  icon.value     = b.icon ?? DEFAULT_ICON_EXPENSE
}

function reset() {
  label.value    = ''
  amount.value   = '0'
  currency.value = 'EUR'
  color.value    = DEFAULT_COLOR
  icon.value     = DEFAULT_ICON_EXPENSE
}

const canSave = computed(() =>
  label.value.trim() !== '' &&
  parseFloat(amount.value) > 0
)

function close() {
  emit('update:modelValue', false)
  reset()
}

async function save() {
  if (!canSave.value) return
  const payload = {
    label:    label.value.trim(),
    amount:   toSubunits(amount.value, CURRENCY_SUBUNIT[currency.value]),
    currency: currency.value,
    color:    color.value,
    icon:     icon.value,
  }
  if (isEditMode.value && props.budget) {
    await update(props.budget.id, payload)
  } else {
    await create(payload)
  }
  emit('saved')
  close()
}

async function deleteBudget() {
  if (!props.budget) return
  await remove(props.budget.id)
  emit('deleted')
  close()
}
</script>

<template>
  <TnsSheet
    :model-value="modelValue"
    :title="isEditMode ? t('budgets.edit') : t('budgets.new')"
    @update:model-value="close"
  >
    <template #closeIcon>
      <ion-icon :icon="closeOutline" style="font-size:20px" />
    </template>

    <TnsFormField :label="t('budgets.label')">
      <input v-model="label" type="text" :placeholder="t('budgets.label')" />
    </TnsFormField>

    <TnsFormField :label="t('budgets.monthlyCap')">
      <input v-model="amount" type="number" min="0" step="0.01" />
    </TnsFormField>

    <TnsFormField :label="t('accounts.currency')">
      <select v-model="currency">
        <option value="EUR">EUR — Euro</option>
        <option value="USD">USD — Dollar</option>
        <option value="GBP">GBP — Livre sterling</option>
        <option value="BTC">BTC — Bitcoin</option>
      </select>
    </TnsFormField>

    <TnsFormField :label="t('transactions.color')">
      <TnsColorPicker v-model="color" />
    </TnsFormField>

    <TnsFormField :label="t('transactions.icon')">
      <TnsIconPicker v-model="icon" />
    </TnsFormField>

    <div class="tns-actions">
      <button v-if="isEditMode" class="tns-delete-btn" @click="deleteBudget">
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
