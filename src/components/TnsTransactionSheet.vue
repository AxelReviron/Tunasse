<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { IonIcon } from '@ionic/vue'
import { closeOutline, trendingUpOutline, trendingDownOutline } from 'ionicons/icons'

import TnsSheet        from '@/components/ui/TnsSheet.vue'
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
import { CURRENCY_SUBUNIT, toSubunits } from '@/constants/currencies'
import type { RecurringUnit } from '@/types'

const props = defineProps<{ modelValue: boolean }>()
const emit  = defineEmits<{
  'update:modelValue': [value: boolean]
  'saved': []
}>()

const { t } = useI18n()
const { accounts }     = useAccounts()
const { budgets }      = useBudgets()
const { create }       = useTransactions()

const today = new Date().toISOString().slice(0, 10)

const type              = ref<'income' | 'expense'>('expense')
const amount            = ref('')
const label             = ref('')
const date              = ref(today)
const accountId         = ref<number | ''>('')
const budgetId          = ref<number | ''>('')
const color             = ref(DEFAULT_COLOR)
const icon              = ref(DEFAULT_ICON_EXPENSE)
const isRecurring       = ref(false)
const recurringInterval = ref(1)
const recurringUnit     = ref<RecurringUnit>('month')

const selectedAccount = computed(() =>
  accounts.value.find(a => a.id === Number(accountId.value))
)
const selectedCurrency = computed(() => selectedAccount.value?.currency ?? 'EUR')

watch(type, val => {
  icon.value = val === 'income' ? DEFAULT_ICON_INCOME : DEFAULT_ICON_EXPENSE
})

const canSave = computed(() =>
  label.value.trim() !== '' &&
  toSubunits(amount.value || '0', CURRENCY_SUBUNIT[selectedCurrency.value]) > 0 &&
  accountId.value !== ''
)

function reset() {
  type.value              = 'expense'
  amount.value            = ''
  label.value             = ''
  date.value              = today
  accountId.value         = ''
  budgetId.value          = ''
  color.value             = DEFAULT_COLOR
  icon.value              = DEFAULT_ICON_EXPENSE
  isRecurring.value       = false
  recurringInterval.value = 1
  recurringUnit.value     = 'month'
}

function close() {
  emit('update:modelValue', false)
  reset()
}

async function save() {
  if (!canSave.value) return
  await create({
    type:    type.value,
    amount:  toSubunits(amount.value, CURRENCY_SUBUNIT[selectedCurrency.value]),
    label:   label.value.trim(),
    date:    date.value,
    account_id:          Number(accountId.value),
    budget_id:           budgetId.value !== '' ? Number(budgetId.value) : undefined,
    color:               color.value,
    icon:                icon.value,
    is_recurring:        isRecurring.value || undefined,
    recurring_interval:  isRecurring.value ? recurringInterval.value : undefined,
    recurring_unit:      isRecurring.value ? recurringUnit.value : undefined,
  })
  emit('saved')
  close()
}
</script>

<template>
  <TnsSheet :model-value="modelValue" :title="t('transactions.new')" @update:model-value="close">
    <template #closeIcon>
      <ion-icon :icon="closeOutline" style="font-size:20px" />
    </template>

    <TnsTypeToggle
      v-model="type"
      :income-label="t('transactions.income')"
      :expense-label="t('transactions.expense')"
    >
      <template #incomeIcon><ion-icon :icon="trendingUpOutline" /></template>
      <template #expenseIcon><ion-icon :icon="trendingDownOutline" /></template>
    </TnsTypeToggle>

    <TnsAmountInput v-model="amount" :type="type" />

    <TnsFormField :label="t('transactions.label')">
      <input v-model="label" type="text" :placeholder="t('transactions.label')" />
    </TnsFormField>

    <TnsFormField :label="t('transactions.date')">
      <input v-model="date" type="date" />
    </TnsFormField>

    <TnsFormField :label="t('transactions.account')">
      <select v-model="accountId">
        <option value="" disabled>— {{ t('transactions.account') }} —</option>
        <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.label }}</option>
      </select>
    </TnsFormField>

    <TnsFormField v-if="type === 'expense'" :label="t('transactions.budget')">
      <select v-model="budgetId">
        <option value="">— {{ t('common.noData') }} —</option>
        <option v-for="b in budgets" :key="b.id" :value="b.id">{{ b.label }}</option>
      </select>
    </TnsFormField>

    <TnsFormField :label="t('transactions.isRecurring')">
      <label class="tns-toggle">
        <input v-model="isRecurring" type="checkbox" />
        <span>{{ t('transactions.isRecurring') }}</span>
      </label>
    </TnsFormField>

    <template v-if="isRecurring">
      <TnsFormField :label="t('transactions.recurringUnit.month')">
        <div class="tns-recurring-row">
          <input v-model.number="recurringInterval" type="number" min="1" style="width:60px" />
          <select v-model="recurringUnit">
            <option v-for="u in ['day','week','month','year']" :key="u" :value="u">
              {{ t(`transactions.recurringUnit.${u}`) }}
            </option>
          </select>
        </div>
      </TnsFormField>
    </template>

    <TnsFormField :label="t('transactions.color', 'Color')">
      <TnsColorPicker v-model="color" />
    </TnsFormField>

    <TnsFormField :label="t('transactions.icon', 'Icon')">
      <TnsIconPicker v-model="icon" />
    </TnsFormField>

    <button class="tns-save-btn" :disabled="!canSave" @click="save">
      {{ t('common.save') }}
    </button>
  </TnsSheet>
</template>

<style scoped>
.tns-save-btn {
  width: 100%;
  padding: 16px;
  border: none;
  border-radius: var(--tns-radius-md);
  background: var(--tns-accent);
  color: #fff;
  font-size: 16px;
  font-weight: 600;
  font-family: var(--tns-font);
  cursor: pointer;
  margin-top: 8px;
  opacity: 1;
  transition: opacity .15s;
}
.tns-save-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.tns-toggle {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 15px;
  color: var(--tns-fg);
  padding: 4px 0;
  cursor: pointer;
}
.tns-recurring-row {
  display: flex;
  gap: 10px;
  padding-top: 4px;
}
</style>
