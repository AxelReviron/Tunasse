<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { IonIcon } from '@ionic/vue'
import { closeOutline, trendingUpOutline, trendingDownOutline, swapHorizontalOutline } from 'ionicons/icons'

import TnsSheet        from '@/components/ui/TnsSheet.vue'
import TnsConfirmAlert from '@/components/ui/TnsConfirmAlert.vue'
import TnsTypeToggle   from '@/components/ui/TnsTypeToggle.vue'
import TnsAmountInput  from '@/components/ui/TnsAmountInput.vue'
import TnsFormField    from '@/components/ui/TnsFormField.vue'
import TnsColorPicker  from '@/components/ui/TnsColorPicker.vue'
import TnsIconPicker   from '@/components/ui/TnsIconPicker.vue'

import { useAccounts }     from '@/composables/useAccounts'
import { useBudgets }      from '@/composables/useBudgets'
import { useTransactions } from '@/composables/useTransactions'
import { DEFAULT_COLOR }   from '@/constants/colors'
import { DEFAULT_ICON_INCOME, DEFAULT_ICON_EXPENSE } from '@/constants/icons'
import { CURRENCY_SUBUNIT, toSubunits, fromSubunits } from '@/constants/currencies'
import type { Transaction, TransactionType } from '@/types'

const props = defineProps<{
  modelValue: boolean
  transaction?: Transaction
}>()
const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  'saved': []
  'deleted': []
}>()

const { t } = useI18n()
const { accounts }                         = useAccounts()
const { budgets }                          = useBudgets()
const { create, update, remove, createTransfer, updateTransfer, removeTransfer } = useTransactions()

const today = new Date().toISOString().slice(0, 10)

const type              = ref<TransactionType>('expense')
const amount            = ref('')
const label             = ref('')
const date              = ref(today)
const accountId         = ref<string | ''>('')
const toAccountId       = ref<string | ''>('')
const budgetId          = ref<string | ''>('')
const color             = ref(DEFAULT_COLOR)
const icon              = ref(DEFAULT_ICON_EXPENSE)

const isEditMode  = computed(() => !!props.transaction)
const isTransfer  = computed(() => type.value === 'transfer')

const selectedAccount = computed(() =>
  accounts.value.find(a => a.id === accountId.value)
)
const selectedCurrency = computed(() => selectedAccount.value?.currency ?? 'EUR')

const compatibleToAccounts = computed(() =>
  accounts.value.filter(a =>
    a.id !== accountId.value &&
    a.currency === selectedAccount.value?.currency
  )
)

watch(type, val => {
  if (!isEditMode.value)
    icon.value = val === 'income' ? DEFAULT_ICON_INCOME : DEFAULT_ICON_EXPENSE
})

watch(() => props.modelValue, open => {
  if (!open) return
  if (props.transaction) fill(props.transaction)
  else reset()
})

function fill(tx: Transaction) {
  type.value              = tx.transfer_peer_id !== undefined ? 'transfer' : tx.type
  accountId.value         = tx.account_id
  toAccountId.value       = tx.to_account_id ?? ''
  amount.value            = fromSubunits(tx.amount, CURRENCY_SUBUNIT[selectedCurrency.value] ?? 100)
  label.value             = tx.label
  date.value              = tx.date
  budgetId.value          = tx.budget_id ?? ''
  color.value             = tx.color ?? DEFAULT_COLOR
  icon.value              = tx.icon ?? (tx.type === 'income' ? DEFAULT_ICON_INCOME : DEFAULT_ICON_EXPENSE)
}

const canSave = computed(() => {
  const amountOk = parseFloat(amount.value) > 0 && accountId.value !== ''
  if (isTransfer.value) {
    return amountOk && toAccountId.value !== '' && toAccountId.value !== accountId.value
  }
  return amountOk && label.value.trim() !== ''
})

function reset() {
  type.value              = 'expense'
  amount.value            = ''
  label.value             = ''
  date.value              = today
  accountId.value         = ''
  toAccountId.value       = ''
  budgetId.value          = ''
  color.value             = DEFAULT_COLOR
  icon.value              = DEFAULT_ICON_EXPENSE
}

function close() {
  emit('update:modelValue', false)
  reset()
}

async function save() {
  if (!canSave.value) return

  if (isTransfer.value) {
    const payload = {
      label:           label.value.trim() || t('transactions.transfer'),
      amount:          toSubunits(amount.value, CURRENCY_SUBUNIT[selectedCurrency.value]),
      date:            date.value,
      from_account_id: accountId.value as string,
      to_account_id:   toAccountId.value as string,
    }
    if (isEditMode.value && props.transaction) {
      await updateTransfer(props.transaction.id, payload)
    } else {
      await createTransfer(payload)
    }
  } else {
    const payload = {
      type:               type.value,
      amount:             toSubunits(amount.value, CURRENCY_SUBUNIT[selectedCurrency.value]),
      label:              label.value.trim(),
      date:               date.value,
      account_id:         accountId.value as string,
      budget_id:          budgetId.value !== '' ? budgetId.value : undefined,
      color:              color.value,
      icon:               icon.value,
    }
    if (isEditMode.value && props.transaction) {
      await update(props.transaction.id, payload)
    } else {
      await create(payload)
    }
  }

  emit('saved')
  close()
}

const showConfirm = ref(false)

function deleteTransaction() {
  if (!props.transaction) return
  showConfirm.value = true
}

async function handleDeleteConfirmed() {
  if (props.transaction!.transfer_peer_id !== undefined) {
    await removeTransfer(props.transaction!.id)
  } else {
    await remove(props.transaction!.id)
  }
  emit('deleted')
  close()
}
</script>

<template>
  <TnsSheet
    :model-value="modelValue"
    :title="isEditMode ? t('transactions.edit') : t('transactions.new')"
    @update:model-value="close"
  >
    <template #closeIcon>
      <ion-icon :icon="closeOutline" style="font-size:20px" />
    </template>

    <TnsTypeToggle
      v-model="type"
      :income-label="t('transactions.income')"
      :expense-label="t('transactions.expense')"
      :transfer-label="t('transactions.transfer')"
    >
      <template #incomeIcon><ion-icon :icon="trendingUpOutline" /></template>
      <template #expenseIcon><ion-icon :icon="trendingDownOutline" /></template>
      <template #transferIcon><ion-icon :icon="swapHorizontalOutline" /></template>
    </TnsTypeToggle>

    <TnsAmountInput v-model="amount" :type="type" />

    <TnsFormField v-if="!isTransfer" :label="t('transactions.label')">
      <input v-model="label" type="text" :placeholder="t('transactions.label')" />
    </TnsFormField>

    <TnsFormField :label="t('transactions.date')">
      <input v-model="date" type="date" />
    </TnsFormField>

    <TnsFormField :label="isTransfer ? t('transactions.transferFrom') : t('transactions.account')">
      <select v-model="accountId">
        <option value="" disabled>— {{ t('transactions.account') }} —</option>
        <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.label }}</option>
      </select>
    </TnsFormField>

    <template v-if="isTransfer">
      <TnsFormField :label="t('transactions.transferTo')">
        <select v-model="toAccountId" :disabled="!selectedAccount">
          <option value="" disabled>— {{ t('transactions.account') }} —</option>
          <option v-for="a in compatibleToAccounts" :key="a.id" :value="a.id">{{ a.label }}</option>
        </select>
      </TnsFormField>
      <p v-if="selectedAccount && !compatibleToAccounts.length" class="tns-currency-warn">
        {{ t('transactions.noCurrencyMatch') }}
      </p>
    </template>

    <template v-if="!isTransfer">
      <TnsFormField v-if="type === 'expense'" :label="t('transactions.budget')">
        <select v-model="budgetId">
          <option value="">— {{ t('common.noData') }} —</option>
          <option v-for="b in budgets" :key="b.id" :value="b.id">{{ b.label }}</option>
        </select>
      </TnsFormField>

      <TnsFormField :label="t('transactions.color')">
        <TnsColorPicker v-model="color" />
      </TnsFormField>

      <TnsFormField :label="t('transactions.icon')">
        <TnsIconPicker v-model="icon" />
      </TnsFormField>
    </template>

    <div class="tns-actions">
      <button v-if="isEditMode" class="tns-delete-btn" @click="deleteTransaction">
        {{ t('common.delete') }}
      </button>
      <button class="tns-save-btn" :disabled="!canSave" @click="save">
        {{ t('common.save') }}
      </button>
    </div>
  </TnsSheet>

  <TnsConfirmAlert
    v-model="showConfirm"
    :title="t('transactions.deleteConfirm')"
    :message="t('transactions.deleteConfirmMessage')"
    :confirm-label="t('common.delete')"
    :cancel-label="t('common.cancel')"
    @confirm="handleDeleteConfirmed"
  />
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
  opacity: 1;
  transition: opacity .15s;
}
.tns-save-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
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
.tns-currency-warn {
  font-size: 13px;
  color: var(--tns-red);
  font-family: var(--tns-font);
  padding: 0 4px;
  margin: -8px 0 8px;
}
</style>
