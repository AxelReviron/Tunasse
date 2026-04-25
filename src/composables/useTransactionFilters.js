// useTransactionFilters — filter + group-by-day helpers for transaction lists.
// Pass a reactive list of transactions and get a filtered + grouped result.

import { computed, ref } from 'vue';
import { useFormat } from './useFormat.js';

export function useTransactionFilters(transactions) {
  const filter = ref('all');  // 'all' | 'income' | 'expense' | 'recurring'
  const query  = ref('');
  const { fmtDay } = useFormat();

  const filtered = computed(() => {
    let list = [...(transactions.value || [])].sort(
      (a, b) => String(b.date).localeCompare(String(a.date))
    );
    if (filter.value === 'income')    list = list.filter(t => t.type === 'income');
    if (filter.value === 'expense')   list = list.filter(t => t.type === 'expense');
    if (filter.value === 'recurring') list = list.filter(t => t.is_recurring);
    if (query.value) {
      const q = query.value.toLowerCase();
      list = list.filter(t =>
        (t.label || '').toLowerCase().includes(q) ||
        (t.location || '').toLowerCase().includes(q)
      );
    }
    return list;
  });

  const grouped = computed(() => {
    const out = {};
    filtered.value.forEach(t => {
      const k = fmtDay(t.date);
      (out[k] = out[k] || []).push(t);
    });
    return out;
  });

  return { filter, query, filtered, grouped };
}
