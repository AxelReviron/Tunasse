import { db } from './database'

export async function seed() {
  const accountCount = await db.accounts.count()
  if (accountCount > 0) {
    console.log('Already seeded, skip')
    return
  }

  const [account1, account2] = await Promise.all([
    db.accounts.add({
      label:    'Compte courant',
      currency: 'EUR',
      type:     'checking',
      balance:  320000,
      color:    '#6366F1',
    }),
    db.accounts.add({
      label:    'Livret A',
      currency: 'EUR',
      type:     'savings',
      balance:  800000,
      color:    '#22C55E',
    }),
  ])

  const [budget1, budget2, budget3, budget4] = await Promise.all([
    db.budgets.add({
      label:    'Courses',
      color:    '#F97316',
      amount:   40000,
      currency: 'EUR',
    }),
    db.budgets.add({
      label:    'Restaurant',
      color:    '#F43F5E',
      amount:   15000,
      currency: 'EUR',
    }),
    db.budgets.add({
      label:    'Logement',
      color:    '#6366F1',
      amount:   90000,
      currency: 'EUR',
    }),
    db.budgets.add({
      label:    'Transport',
      color:    '#0EA5E9',
      amount:   10000,
      currency: 'EUR',
    }),
  ])

  await Promise.all([
    db.transactions.add({
      label:      'Carrefour',
      amount:     5430,
      type:       'expense',
      date:       '2026-04-23',
      account_id: account1,
      budget_id:  budget1,
      category:   'Courses',
    }),
    db.transactions.add({
      label:      'Salaire avril',
      amount:     280000,
      type:       'income',
      date:       '2026-04-23',
      account_id: account1,
    }),
    db.transactions.add({
      label:      'Sushi Shop',
      amount:     3200,
      type:       'expense',
      date:       '2026-04-22',
      account_id: account1,
      budget_id:  budget2,
      category:   'Restaurant',
    }),
    db.transactions.add({
      label:              'Loyer',
      amount:             90000,
      type:               'expense',
      date:               '2026-04-22',
      account_id:         account1,
      budget_id:          budget3,
      category:           'Logement',
      is_recurring:       true,
      recurring_interval: 1,
      recurring_unit:     'month',
    }),
    db.transactions.add({
      label:      'Lidl',
      amount:     3810,
      type:       'expense',
      date:       '2026-04-21',
      account_id: account1,
      budget_id:  budget1,
      category:   'Courses',
    }),
    db.transactions.add({
      label:              'Navigo mensuel',
      amount:             8640,
      type:               'expense',
      date:               '2026-04-20',
      account_id:         account1,
      budget_id:          budget4,
      category:           'Transport',
      is_recurring:       true,
      recurring_interval: 1,
      recurring_unit:     'month',
    }),
    db.transactions.add({
      label:      'Virement épargne',
      amount:     20000,
      type:       'expense',
      date:       '2026-04-20',
      account_id: account1,
    }),
  ])

  console.log('Db seeded')
}
