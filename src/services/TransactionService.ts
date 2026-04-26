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

  async createTransfer(payload: {
    label: string
    amount: number
    date: string
    from_account_id: number
    to_account_id: number
  }): Promise<void> {
    const [fromAcc, toAcc] = await Promise.all([
      db.accounts.get(payload.from_account_id),
      db.accounts.get(payload.to_account_id),
    ])
    if (!fromAcc) throw new Error(`Account not found: ${payload.from_account_id}`)
    if (!toAcc)   throw new Error(`Account not found: ${payload.to_account_id}`)
    if (fromAcc.currency !== toAcc.currency)
      throw new Error('Both accounts must share the same currency')
    if (payload.amount <= 0) throw new Error('Amount must be positive')

    const date = new Date(payload.date).toISOString().slice(0, 10)

    const expenseId = await db.transactions.add({
      label:            payload.label,
      amount:           payload.amount,
      type:             'expense',
      date,
      account_id:       payload.from_account_id,
      to_account_id:    payload.to_account_id,
      transfer_peer_id: 0,
    } as Transaction)

    const incomeId = await db.transactions.add({
      label:            payload.label,
      amount:           payload.amount,
      type:             'income',
      date,
      account_id:       payload.to_account_id,
      transfer_peer_id: expenseId,
    } as Transaction)

    await db.transactions.update(expenseId, { transfer_peer_id: incomeId })
  },

  async updateTransfer(expenseId: number, changes: {
    label?: string
    amount?: number
    date?: string
    from_account_id?: number
    to_account_id?: number
  }): Promise<void> {
    const expense = await db.transactions.get(expenseId)
    if (!expense?.transfer_peer_id) throw new Error('Transfer not found')

    if (changes.from_account_id !== undefined || changes.to_account_id !== undefined) {
      const fromId = changes.from_account_id ?? expense.account_id
      const toId   = changes.to_account_id   ?? expense.to_account_id!
      const [fromAcc, toAcc] = await Promise.all([
        db.accounts.get(fromId),
        db.accounts.get(toId),
      ])
      if (fromAcc && toAcc && fromAcc.currency !== toAcc.currency)
        throw new Error('Both accounts must share the same currency')
    }

    const date = changes.date
      ? new Date(changes.date).toISOString().slice(0, 10)
      : undefined

    const expenseChanges: Partial<Transaction> = {}
    const incomeChanges:  Partial<Transaction> = {}

    if (changes.label  !== undefined) { expenseChanges.label  = changes.label;  incomeChanges.label  = changes.label }
    if (changes.amount !== undefined) { expenseChanges.amount = changes.amount; incomeChanges.amount = changes.amount }
    if (date           !== undefined) { expenseChanges.date   = date;           incomeChanges.date   = date }
    if (changes.from_account_id !== undefined) expenseChanges.account_id = changes.from_account_id
    if (changes.to_account_id   !== undefined) {
      expenseChanges.to_account_id = changes.to_account_id
      incomeChanges.account_id     = changes.to_account_id
    }

    await Promise.all([
      db.transactions.update(expenseId, expenseChanges),
      db.transactions.update(expense.transfer_peer_id, incomeChanges),
    ])
  },

  async removeTransfer(id: number): Promise<void> {
    const tx = await db.transactions.get(id)
    const peerId = tx?.transfer_peer_id
    await db.transactions.delete(id)
    if (peerId) await db.transactions.delete(peerId)
  },
}
