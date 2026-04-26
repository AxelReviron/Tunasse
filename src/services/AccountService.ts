import { db } from '@/db/database'
import type { Account, Currency } from '@/types'

export type BalanceEntry = { currency: Currency; total: number }

export const AccountService = {
  getAll(): Promise<Account[]> {
    return db.accounts.toArray()
  },

  getById(id: number): Promise<Account | undefined> {
    return db.accounts.get(id)
  },

  getByType(type: Account['type']): Promise<Account[]> {
    return db.accounts.where('type').equals(type).toArray()
  },

  async getRealBalance(accountId: number): Promise<number> {
    const account = await db.accounts.get(accountId)
    if (!account) return 0

    const transactions = await db.transactions.where('account_id').equals(accountId).toArray()
    const delta = transactions.reduce((sum, tx) =>
      sum + (tx.type === 'income' ? tx.amount : -tx.amount), 0
    )

    return account.balance + delta
  },

  async getTotalBalance(): Promise<BalanceEntry[]> {
    const accounts = await db.accounts.toArray()
    const grouped: Partial<Record<Currency, number>> = {}

    for (const account of accounts) {
      const balance = await AccountService.getRealBalance(account.id)
      grouped[account.currency] = (grouped[account.currency] ?? 0) + balance
    }

    return Object.entries(grouped).map(([currency, total]) => ({
      currency: currency as Currency,
      total: total!,
    }))
  },

  create(account: Omit<Account, 'id'>): Promise<number> {
    return db.accounts.add(account as Account)
  },

  update(id: number, changes: Partial<Omit<Account, 'id'>>): Promise<number> {
    return db.accounts.update(id, changes)
  },

  async remove(id: number): Promise<void> {
    await db.transactions.where('account_id').equals(id).delete()
    await db.accounts.delete(id)
  },
}
