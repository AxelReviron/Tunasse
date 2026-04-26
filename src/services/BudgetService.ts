import { db } from '@/db/database'
import type { Budget } from '@/types'

export type BudgetWithSpent = Budget & { spent: number }

export const BudgetService = {
  async getAll(): Promise<BudgetWithSpent[]> {
    const budgets = await db.budgets.toArray()
    return Promise.all(budgets.map(b => BudgetService.withSpent(b)))
  },

  async getById(id: number): Promise<BudgetWithSpent | undefined> {
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

  create(budget: Omit<Budget, 'id'>): Promise<number> {
    return db.budgets.add(budget as Budget)
  },

  update(id: number, changes: Partial<Omit<Budget, 'id'>>): Promise<number> {
    return db.budgets.update(id, changes)
  },

  async remove(id: number): Promise<void> {
    await db.transactions.where('budget_id').equals(id).modify({ budget_id: undefined })
    await db.budgets.delete(id)
  },
}
