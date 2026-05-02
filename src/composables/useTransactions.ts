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

  const today = new Date().toISOString().slice(0, 10)

  const thisMonthTransactions = computed(() => {
    const now = new Date()
    const prefix = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`
    return transactions.value.filter(tx => tx.date.startsWith(prefix))
  })

  // Transactions du mois courant dont la date est <= aujourd'hui (exclut les futures)
  const effectiveMonthTransactions = computed(() => {
    return thisMonthTransactions.value.filter(tx => tx.date <= today)
  })

  const monthIncome = computed(() =>
    effectiveMonthTransactions.value
      .filter(tx => tx.type === 'income' && !tx.transfer_peer_id)
      .reduce((sum, tx) => sum + tx.amount, 0)
  )

  const monthExpense = computed(() =>
    effectiveMonthTransactions.value
      .filter(tx => tx.type === 'expense' && !tx.transfer_peer_id)
      .reduce((sum, tx) => sum + tx.amount, 0)
  )

  const recent = computed(() =>
    [...transactions.value]
      .filter(tx => !(tx.type === 'income' && tx.transfer_peer_id !== undefined))
      .sort((a, b) => b.date.localeCompare(a.date))
      .slice(0, 5)
  )

  const alreadyPaid = computed(() => {
    const today = new Date().toISOString().slice(0, 10)
    return thisMonthTransactions.value
      .filter(tx => tx.type === 'expense' && tx.is_recurring && !tx.transfer_peer_id && tx.date <= today)
      .reduce((sum, tx) => sum + tx.amount, 0)
  })

  const toPay = computed(() => {
    const today = new Date().toISOString().slice(0, 10)
    return thisMonthTransactions.value
      .filter(tx => tx.type === 'expense' && tx.is_recurring && !tx.transfer_peer_id && tx.date > today)
      .reduce((sum, tx) => sum + tx.amount, 0)
  })

  function getByAccount(accountId: string): Transaction[] {
    return transactions.value.filter(tx => tx.account_id === accountId)
  }

  function getByBudget(budgetId: string): Transaction[] {
    return transactions.value.filter(tx => tx.budget_id === budgetId)
  }

  async function create(transaction: Omit<Transaction, 'id' | 'createdAt' | 'updatedAt'>) {
    return TransactionService.create(transaction)
  }

  async function update(id: string, changes: Partial<Omit<Transaction, 'id' | 'createdAt' | 'updatedAt'>>) {
    return TransactionService.update(id, changes)
  }

  async function remove(id: string) {
    return TransactionService.remove(id)
  }

  async function createTransfer(payload: Parameters<typeof TransactionService.createTransfer>[0]) {
    return TransactionService.createTransfer(payload)
  }

  async function updateTransfer(id: string, changes: Parameters<typeof TransactionService.updateTransfer>[1]) {
    return TransactionService.updateTransfer(id, changes)
  }

  async function removeTransfer(id: string) {
    return TransactionService.removeTransfer(id)
  }

  return {
    transactions, isLoading, today,
    thisMonthTransactions, effectiveMonthTransactions, monthIncome, monthExpense, alreadyPaid, toPay, recent,
    getByAccount, getByBudget,
    create, update, remove,
    createTransfer, updateTransfer, removeTransfer,
  }
}
