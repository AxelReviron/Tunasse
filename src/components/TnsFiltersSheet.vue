<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { IonIcon } from '@ionic/vue'
import { closeOutline } from 'ionicons/icons'

import TnsSheet      from '@/components/ui/TnsSheet.vue'
import TnsFilterChips from '@/components/ui/TnsFilterChips.vue'

type FilterValue    = 'all' | 'income' | 'expense' | 'recurring' | 'transfer'
type DateRangeValue = 'all' | 'thisMonth' | 'lastMonth' | 'thisYear'

defineProps<{
  modelValue: boolean
  filter:     FilterValue
  dateRange:  DateRangeValue
}>()

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  'update:filter':     [value: FilterValue]
  'update:dateRange':  [value: DateRangeValue]
  'reset': []
}>()

const { t } = useI18n()
</script>

<template>
  <TnsSheet :model-value="modelValue" :title="t('common.filters')" @update:model-value="emit('update:modelValue', $event)">
    <template #closeIcon>
      <ion-icon :icon="closeOutline" style="font-size:20px" />
    </template>

    <div class="tns-filter-group">
      <div class="tns-filter-group-label">{{ t('transactions.type') }}</div>
      <TnsFilterChips
        :model-value="filter"
        :chips="[
          { value: 'all',       label: t('transactions.all') },
          { value: 'income',    label: t('transactions.income') },
          { value: 'expense',   label: t('transactions.expense') },
          { value: 'recurring', label: t('transactions.recurring') },
          { value: 'transfer',  label: t('transactions.transfer') },
        ]"
        @update:model-value="emit('update:filter', $event as FilterValue)"
      />
    </div>

    <div class="tns-filter-group">
      <div class="tns-filter-group-label">{{ t('transactions.period') }}</div>
      <TnsFilterChips
        :model-value="dateRange"
        :chips="[
          { value: 'all',       label: t('transactions.dateRange.all') },
          { value: 'thisMonth', label: t('transactions.dateRange.thisMonth') },
          { value: 'lastMonth', label: t('transactions.dateRange.lastMonth') },
          { value: 'thisYear',  label: t('transactions.dateRange.thisYear') },
        ]"
        @update:model-value="emit('update:dateRange', $event as DateRangeValue)"
      />
    </div>

    <button class="tns-reset-btn" @click="emit('reset')">
      {{ t('common.reset') }}
    </button>
  </TnsSheet>
</template>

<style scoped>
.tns-filter-group {
  margin-bottom: 16px;
}
.tns-filter-group-label {
  font-size: 11.5px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.2px;
  color: var(--tns-fg2);
  font-family: var(--tns-font);
  padding: 0 16px;
  margin-bottom: 8px;
}
.tns-reset-btn {
  width: 100%;
  padding: 14px;
  border: 1.5px solid var(--tns-sep);
  border-radius: var(--tns-radius-md);
  background: transparent;
  color: var(--tns-fg2);
  font-size: 15px;
  font-weight: 500;
  font-family: var(--tns-font);
  cursor: pointer;
  margin-top: 8px;
}
</style>
