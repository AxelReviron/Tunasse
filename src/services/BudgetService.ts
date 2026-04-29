import { db } from '@/db/database'
import type { Budget } from '@/types'

export type BudgetWithSpent = Budget & { spent: number }

type BudgetCreate = Omit<Budget, 'id' | 'createdAt' | 'updatedAt'>
type BudgetUpdate = Partial<Omit<Budget, 'id' | 'createdAt' | 'updatedAt'>>

export const BudgetService = {
  async getAll(): Promise<BudgetWithSpent[]> {
    const budgets = await db.budgets.toArray()
    return Promise.all(budgets.map(b => BudgetService.withSpent(b)))
  },

  async getById(id: string): Promise<BudgetWithSpent | undefined> {
    const budget = await db.budgets.get(id)
    if (!budget) return undefined
    return BudgetService.withSpent(budget)
  },

  async withSpent(budget: Budget): Promise<BudgetWithSpent> {
    const now    = new Date()
    const prefix = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`

    const transactions = await db.transactions
      .where('budget_id').equals(budget.id)
      .filter(tx => tx.date.startsWith(prefix))
      .toArray()

    const spent = transactions.reduce((sum, tx) => sum + tx.amount, 0)
    return { ...budget, spent }
  },

  create(budget: BudgetCreate): Promise<string> {
    const now = new Date().toISOString()
    return db.budgets.add({
      ...budget,
      id: crypto.randomUUID(),
      createdAt: now,
      updatedAt: now,
    })
  },

  update(id: string, changes: BudgetUpdate): Promise<number> {
    return db.budgets.update(id, { ...changes, updatedAt: new Date().toISOString() })
  },

  async remove(id: string): Promise<void> {
    const now = new Date().toISOString()
    await db.transactions.where('budget_id').equals(id).modify({ budget_id: undefined, updatedAt: now })
    await db.budgets.delete(id)
    await db.deletions.add({ id: crypto.randomUUID(), tableName: 'budgets', recordId: id, deletedAt: now })
  },
}
