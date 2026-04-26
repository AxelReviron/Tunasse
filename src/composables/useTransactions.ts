import { ref, computed, onUnmounted } from 'vue'
import { liveQuery } from 'dexie'
import { TransactionService } from '@/services/TransactionService'
import type { Transaction } from '@/types'

export function useTransactions() {
  const transactions = ref<Transaction[]>([])
  const isLoading    = ref(true)

  const subscription = liveQuery(() => TransactionService.getAll())
    .subscribe({
      next:  result => { transactions.value = result; isLoading.value = false },
      error: err    => { console.error(err);           isLoading.value = false },
    })

  onUnmounted(() => subscription.unsubscribe())

  const thisMonthTransactions = computed(() => {
    const now = new Date()
    const prefix = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`
    return transactions.value.filter(tx => tx.date.startsWith(prefix))
  })

  const monthIncome = computed(() =>
    thisMonthTransactions.value
      .filter(tx => tx.type === 'income')
      .reduce((sum, tx) => sum + tx.amount, 0)
  )

  const monthExpense = computed(() =>
    thisMonthTransactions.value
      .filter(tx => tx.type === 'expense')
      .reduce((sum, tx) => sum + tx.amount, 0)
  )

  const recent = computed(() =>
    [...transactions.value]
      .sort((a, b) => b.date.localeCompare(a.date))
      .slice(0, 5)
  )

  function getByAccount(accountId: number): Transaction[] {
    return transactions.value.filter(tx => tx.account_id === accountId)
  }

  function getByBudget(budgetId: number): Transaction[] {
    return transactions.value.filter(tx => tx.budget_id === budgetId)
  }

  async function create(transaction: Omit<Transaction, 'id'>) {
    return TransactionService.create(transaction)
  }

  async function update(id: number, changes: Partial<Omit<Transaction, 'id'>>) {
    return TransactionService.update(id, changes)
  }

  async function remove(id: number) {
    return TransactionService.remove(id)
  }

  return {
    transactions, isLoading,
    thisMonthTransactions, monthIncome, monthExpense, recent,
    getByAccount, getByBudget,
    create, update, remove,
  }
}
