import { computed, ref, type Ref } from 'vue'
import type { Transaction } from '@/types'
import { useFormat } from './useFormat'

type FilterValue = 'all' | 'income' | 'expense' | 'recurring'

export function useTransactionFilters(transactions: Ref<Transaction[]>) {
  const filter = ref<FilterValue>('all')
  const query  = ref('')
  const { fmtDay } = useFormat()

  const filtered = computed(() => {
    let list = [...transactions.value].sort(
      (a, b) => b.date.localeCompare(a.date)
    )
    if (filter.value === 'income')    list = list.filter(t => t.type === 'income')
    if (filter.value === 'expense')   list = list.filter(t => t.type === 'expense')
    if (filter.value === 'recurring') list = list.filter(t => t.is_recurring)
    if (query.value) {
      const q = query.value.toLowerCase()
      list = list.filter(t =>
        t.label.toLowerCase().includes(q) ||
        (t.location ?? '').toLowerCase().includes(q)
      )
    }
    return list
  })

  const grouped = computed(() => {
    const out: Record<string, Transaction[]> = {}
    filtered.value.forEach(t => {
      const k = fmtDay(t.date)
      ;(out[k] ??= []).push(t)
    })
    return out
  })

  return { filter, query, filtered, grouped }
}
