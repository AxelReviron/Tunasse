import { db } from '@/db/database'
import type { Account, Budget, Transaction, Deletion } from '@/types'

export const SCHEMA_VERSION = 3

export type SyncDump = {
  schemaVersion: number
  accounts: Account[]
  budgets: Budget[]
  transactions: Transaction[]
  deletions: Deletion[]
}

export type MergeResult = { ok: boolean; error?: string }

export const SyncService = {
  async exportDump(): Promise<SyncDump> {
    const [accounts, budgets, transactions, deletions] = await Promise.all([
      db.accounts.toArray(),
      db.budgets.toArray(),
      db.transactions.toArray(),
      db.deletions.toArray(),
    ])
    return { schemaVersion: SCHEMA_VERSION, accounts, budgets, transactions, deletions }
  },

  async mergeDump(remote: SyncDump): Promise<MergeResult> {
    if (remote.schemaVersion !== SCHEMA_VERSION) {
      return { ok: false, error: `schema_mismatch:local=${SCHEMA_VERSION},remote=${remote.schemaVersion}` }
    }

    const [localAccounts, localBudgets, localTxs] = await Promise.all([
      db.accounts.toArray(),
      db.budgets.toArray(),
      db.transactions.toArray(),
    ])

    const accountMap = new Map(localAccounts.map(a => [a.id, a]))
    const budgetMap  = new Map(localBudgets.map(b => [b.id, b]))
    const txMap      = new Map(localTxs.map(t => [t.id, t]))

    const wins = <T extends { id: string; updatedAt: string }>(
      remotes: T[],
      localMap: Map<string, T>,
    ) => remotes.filter(r => { const l = localMap.get(r.id); return !l || r.updatedAt > l.updatedAt })

    const accountsToUpsert = wins(remote.accounts, accountMap)
    const budgetsToUpsert  = wins(remote.budgets, budgetMap)
    const txsToUpsert      = wins(remote.transactions, txMap)

    const toDelete: Record<string, string[]> = { accounts: [], budgets: [], transactions: [] }
    for (const d of remote.deletions) toDelete[d.tableName].push(d.recordId)

    await db.transaction('rw', db.accounts, db.budgets, db.transactions, async () => {
      if (accountsToUpsert.length) await db.accounts.bulkPut(accountsToUpsert)
      if (budgetsToUpsert.length)  await db.budgets.bulkPut(budgetsToUpsert)
      if (txsToUpsert.length)      await db.transactions.bulkPut(txsToUpsert)

      if (toDelete.accounts.length)     await db.accounts.bulkDelete(toDelete.accounts)
      if (toDelete.budgets.length)      await db.budgets.bulkDelete(toDelete.budgets)
      if (toDelete.transactions.length) await db.transactions.bulkDelete(toDelete.transactions)
    })

    return { ok: true }
  },

  clearDeletions(): Promise<void> {
    return db.deletions.clear()
  },
}
