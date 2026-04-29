import { db } from './database'

export async function seed() {
  const accountCount = await db.accounts.count()
  if (accountCount > 0) {
    console.log('Already seeded, skip')
    return
  }

  const now = new Date().toISOString()

  const account1Id = crypto.randomUUID()
  const account2Id = crypto.randomUUID()

  await Promise.all([
    db.accounts.add({
      id:        account1Id,
      label:     'Compte courant',
      currency:  'EUR',
      type:      'checking',
      balance:   320000,
      color:     '#6366F1',
      createdAt: now,
      updatedAt: now,
    }),
    db.accounts.add({
      id:        account2Id,
      label:     'Livret A',
      currency:  'EUR',
      type:      'savings',
      balance:   800000,
      color:     '#22C55E',
      createdAt: now,
      updatedAt: now,
    }),
  ])

  const budget1Id = crypto.randomUUID()
  const budget2Id = crypto.randomUUID()
  const budget3Id = crypto.randomUUID()
  const budget4Id = crypto.randomUUID()

  await Promise.all([
    db.budgets.add({ id: budget1Id, label: 'Courses',    color: '#F97316', amount: 40000, currency: 'EUR', createdAt: now, updatedAt: now }),
    db.budgets.add({ id: budget2Id, label: 'Restaurant', color: '#F43F5E', amount: 15000, currency: 'EUR', createdAt: now, updatedAt: now }),
    db.budgets.add({ id: budget3Id, label: 'Logement',   color: '#6366F1', amount: 90000, currency: 'EUR', createdAt: now, updatedAt: now }),
    db.budgets.add({ id: budget4Id, label: 'Transport',  color: '#0EA5E9', amount: 10000, currency: 'EUR', createdAt: now, updatedAt: now }),
  ])

  await Promise.all([
    db.transactions.add({ id: crypto.randomUUID(), label: 'Carrefour',      amount: 5430,   type: 'expense', date: '2026-04-23', account_id: account1Id, budget_id: budget1Id, category: 'Courses',   createdAt: now, updatedAt: now }),
    db.transactions.add({ id: crypto.randomUUID(), label: 'Salaire avril',  amount: 280000, type: 'income',  date: '2026-04-23', account_id: account1Id,                                               createdAt: now, updatedAt: now }),
    db.transactions.add({ id: crypto.randomUUID(), label: 'Sushi Shop',     amount: 3200,   type: 'expense', date: '2026-04-22', account_id: account1Id, budget_id: budget2Id, category: 'Restaurant', createdAt: now, updatedAt: now }),
    db.transactions.add({ id: crypto.randomUUID(), label: 'Loyer',          amount: 90000,  type: 'expense', date: '2026-04-22', account_id: account1Id, budget_id: budget3Id, category: 'Logement',   createdAt: now, updatedAt: now, is_recurring: true, recurring_interval: 1, recurring_unit: 'month' }),
    db.transactions.add({ id: crypto.randomUUID(), label: 'Lidl',           amount: 3810,   type: 'expense', date: '2026-04-21', account_id: account1Id, budget_id: budget1Id, category: 'Courses',   createdAt: now, updatedAt: now }),
    db.transactions.add({ id: crypto.randomUUID(), label: 'Navigo mensuel', amount: 8640,   type: 'expense', date: '2026-04-20', account_id: account1Id, budget_id: budget4Id, category: 'Transport',  createdAt: now, updatedAt: now, is_recurring: true, recurring_interval: 1, recurring_unit: 'month' }),
    db.transactions.add({ id: crypto.randomUUID(), label: 'Virement épargne', amount: 20000, type: 'expense', date: '2026-04-20', account_id: account1Id,                                              createdAt: now, updatedAt: now }),
  ])

  console.log('Db seeded')
}
