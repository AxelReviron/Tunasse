import Dexie, { type EntityTable } from 'dexie'
import type { Account, Budget, Deletion, Transaction } from '@/types'

const db = new Dexie('TunasseDB') as Dexie & {
  accounts:     EntityTable<Account, 'id'>
  transactions: EntityTable<Transaction, 'id'>
  budgets:      EntityTable<Budget, 'id'>
  deletions:    EntityTable<Deletion, 'id'>
}

db.version(1).stores({
  accounts:     '++id, type',
  transactions: '++id, account_id, type, date, budget_id, is_recurring',
  budgets:      '++id',
})

db.version(2).stores({
  accounts:     '++id, type',
  transactions: '++id, account_id, type, date, budget_id, is_recurring, transfer_peer_id',
  budgets:      '++id',
})

// v3 : clés primaires UUID (string) — Dexie recrée les tables, IndexedDB est vidée
db.version(3).stores({
  accounts:     'id, type',
  transactions: 'id, account_id, type, date, budget_id, is_recurring, transfer_peer_id',
  budgets:      'id',
  deletions:    'id, tableName, deletedAt',
})

export { db }
