import { db } from '@/db/database'
import type { Transaction } from '@/types'

async function validate(data: Partial<Omit<Transaction, 'id'>>, isCreate = false) {
  if (data.amount !== undefined && data.amount <= 0)
    throw new Error('Amount must be positive')

  if (isCreate || data.account_id !== undefined) {
    const account = await db.accounts.get(data.account_id!)
    if (!account) throw new Error(`Account not found (id: ${data.account_id})`)
  }

  if (data.budget_id !== undefined) {
    const budget = await db.budgets.get(data.budget_id)
    if (!budget) throw new Error(`Budget not found (id: ${data.budget_id})`)
  }

  if (data.is_recurring) {
    if (!data.recurring_unit || !data.recurring_interval)
      throw new Error('recurring_unit and recurring_interval are required for a recurring transaction')
  }
}

export const TransactionService = {
  getAll(): Promise<Transaction[]> {
    return db.transactions.toArray()
  },

  getById(id: number): Promise<Transaction | undefined> {
    return db.transactions.get(id)
  },

  getByAccount(accountId: number): Promise<Transaction[]> {
    return db.transactions.where('account_id').equals(accountId).toArray()
  },

  getByBudget(budgetId: number): Promise<Transaction[]> {
    return db.transactions.where('budget_id').equals(budgetId).toArray()
  },

  getByMonth(year: number, month: number): Promise<Transaction[]> {
    const prefix = `${year}-${String(month).padStart(2, '0')}`
    return db.transactions.where('date').startsWith(prefix).toArray()
  },

  async create(transaction: Omit<Transaction, 'id'>): Promise<number> {
    await validate(transaction, true)
    const normalized = {
      ...transaction,
      date: new Date(transaction.date).toISOString().slice(0, 10),
    }
    return db.transactions.add(normalized as Transaction)
  },

  async update(id: number, changes: Partial<Omit<Transaction, 'id'>>): Promise<number> {
    await validate(changes)
    const normalized = changes.date
      ? { ...changes, date: new Date(changes.date).toISOString().slice(0, 10) }
      : changes
    return db.transactions.update(id, normalized)
  },

  remove(id: number): Promise<void> {
    return db.transactions.delete(id)
  },
}
