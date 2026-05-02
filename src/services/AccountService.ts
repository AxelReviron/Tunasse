import { db } from '@/db/database'
import type { Account, Currency } from '@/types'

export type BalanceEntry = { currency: Currency; total: number }

type AccountCreate = Omit<Account, 'id' | 'createdAt' | 'updatedAt'>
type AccountUpdate = Partial<Omit<Account, 'id' | 'createdAt' | 'updatedAt'>>

export const AccountService = {
  getAll(): Promise<Account[]> {
    return db.accounts.toArray()
  },

  getById(id: string): Promise<Account | undefined> {
    return db.accounts.get(id)
  },

  getByType(type: Account['type']): Promise<Account[]> {
    return db.accounts.where('type').equals(type).toArray()
  },

  async getRealBalance(accountId: string): Promise<number> {
    const account = await db.accounts.get(accountId)
    if (!account) return 0

    const today = new Date().toISOString().slice(0, 10)
    const transactions = await db.transactions
      .where('account_id').equals(accountId)
      .filter(tx => tx.date <= today)
      .toArray()
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

  create(account: AccountCreate): Promise<string> {
    const now = new Date().toISOString()
    return db.accounts.add({
      ...account,
      id: crypto.randomUUID(),
      createdAt: now,
      updatedAt: now,
    })
  },

  update(id: string, changes: AccountUpdate): Promise<number> {
    return db.accounts.update(id, { ...changes, updatedAt: new Date().toISOString() })
  },

  async remove(id: string): Promise<void> {
    const now   = new Date().toISOString()
    const txIds = (await db.transactions.where('account_id').equals(id).primaryKeys()) as string[]

    await db.transactions.where('account_id').equals(id).delete()
    await db.accounts.delete(id)

    await db.deletions.bulkAdd([
      { id: crypto.randomUUID(), tableName: 'accounts',      recordId: id,   deletedAt: now },
      ...txIds.map(txId => ({ id: crypto.randomUUID(), tableName: 'transactions' as const, recordId: txId, deletedAt: now })),
    ])
  },
}
