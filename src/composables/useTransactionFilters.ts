import { computed, ref, type Ref } from 'vue'
import type { Transaction } from '@/types'
import { useFormat } from './useFormat'

type FilterValue    = 'all' | 'income' | 'expense' | 'transfer'
type DateRangeValue = 'all' | 'thisMonth' | 'lastMonth' | 'thisYear'

function prefixes() {
  const now   = new Date()
  const year  = now.getFullYear()
  const month = now.getMonth()
  const thisMonth = `${year}-${String(month + 1).padStart(2, '0')}`
  const lastMonthDate = new Date(year, month - 1, 1)
  const lastMonth = `${lastMonthDate.getFullYear()}-${String(lastMonthDate.getMonth() + 1).padStart(2, '0')}`
  return { thisMonth, lastMonth, thisYear: String(year) }
}

export function useTransactionFilters(transactions: Ref<Transaction[]>) {
  const filter    = ref<FilterValue>('all')
  const dateRange = ref<DateRangeValue>('all')
  const query     = ref('')
  const { fmtDay } = useFormat()

  const filtered = computed(() => {
    let list = [...transactions.value]
      .filter(t => !(t.type === 'income' && t.transfer_peer_id !== undefined))
      .sort((a, b) => b.date.localeCompare(a.date))

    if (filter.value === 'income')    list = list.filter(t => t.type === 'income')
    if (filter.value === 'expense')   list = list.filter(t => t.type === 'expense' && !t.transfer_peer_id)
    if (filter.value === 'transfer')  list = list.filter(t => t.transfer_peer_id !== undefined)


    if (dateRange.value !== 'all') {
      const { thisMonth, lastMonth, thisYear } = prefixes()
      if (dateRange.value === 'thisMonth')  list = list.filter(t => t.date.startsWith(thisMonth))
      if (dateRange.value === 'lastMonth')  list = list.filter(t => t.date.startsWith(lastMonth))
      if (dateRange.value === 'thisYear')   list = list.filter(t => t.date.startsWith(thisYear))
    }

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

  return { filter, dateRange, query, filtered, grouped }
}
