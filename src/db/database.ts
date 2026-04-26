import Dexie, { type EntityTable } from 'dexie'
import type { Account, Budget, Transaction } from '@/types'

const db = new Dexie('TunasseDB') as Dexie & {
  accounts:     EntityTable<Account, 'id'>
  transactions: EntityTable<Transaction, 'id'>
  budgets:      EntityTable<Budget, 'id'>
}

db.version(1).stores({
  accounts:     '++id, type',
  transactions: '++id, account_id, type, date, budget_id, is_recurring',
  budgets:      '++id',
})

export { db }
