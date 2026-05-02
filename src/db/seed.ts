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

  function dateInCurrentMonth(day: number) {
    const date = new Date()
    date.setDate(day)
    return date.toISOString().slice(0, 10)
  }

  await Promise.all([
    db.accounts.add({
      id: account1Id,
      label: 'Checking Account',
      currency: 'EUR',
      type: 'checking',
      balance: 320000,
      color: '#6366F1',
      createdAt: now,
      updatedAt: now,
    }),
    db.accounts.add({
      id: account2Id,
      label: 'Savings Account',
      currency: 'EUR',
      type: 'savings',
      balance: 800000,
      color: '#22C55E',
      createdAt: now,
      updatedAt: now,
    }),
  ])

  const groceryId = crypto.randomUUID()
  const restaurantId = crypto.randomUUID()
  const housingId = crypto.randomUUID()
  const transportId = crypto.randomUUID()

  await Promise.all([
    db.budgets.add({
      id: groceryId,
      label: 'Groceries',
      icon: 'cart-outline',
      color: '#F97316',
      amount: 40000,
      currency: 'EUR',
      createdAt: now,
      updatedAt: now,
    }),
    db.budgets.add({
      id: restaurantId,
      label: 'Dining',
      icon: 'restaurant-outline',
      color: '#F43F5E',
      amount: 15000,
      currency: 'EUR',
      createdAt: now,
      updatedAt: now,
    }),
    db.budgets.add({
      id: housingId,
      label: 'Housing',
      icon: 'home-outline',
      color: '#6366F1',
      amount: 90000,
      currency: 'EUR',
      createdAt: now,
      updatedAt: now,
    }),
    db.budgets.add({
      id: transportId,
      label: 'Transport',
      icon: 'car-outline',
      color: '#0EA5E9',
      amount: 10000,
      currency: 'EUR',
      createdAt: now,
      updatedAt: now,
    }),
  ])

  await Promise.all([
    // Income
    db.transactions.add({
      id: crypto.randomUUID(),
      label: 'Salary',
      amount: 280000,
      type: 'income',
      date: dateInCurrentMonth(1),
      account_id: account1Id,
      createdAt: now,
      updatedAt: now,
    }),

    // Housing (recurring)
    db.transactions.add({
      id: crypto.randomUUID(),
      label: 'Rent',
      amount: 90000,
      type: 'expense',
      date: dateInCurrentMonth(3),
      account_id: account1Id,
      budget_id: housingId,
      category: 'Housing',
      is_recurring: true,
      recurring_interval: 1,
      recurring_unit: 'month',
      createdAt: now,
      updatedAt: now,
    }),

    // Transport (recurring)
    db.transactions.add({
      id: crypto.randomUUID(),
      label: 'Monthly Pass',
      amount: 8640,
      type: 'expense',
      date: dateInCurrentMonth(5),
      account_id: account1Id,
      budget_id: transportId,
      category: 'Transport',
      is_recurring: true,
      recurring_interval: 1,
      recurring_unit: 'month',
      createdAt: now,
      updatedAt: now,
    }),

    // Groceries spread over the month
    db.transactions.add({
      id: crypto.randomUUID(),
      label: 'Carrefour',
      amount: 5400,
      type: 'expense',
      date: dateInCurrentMonth(6),
      account_id: account1Id,
      budget_id: groceryId,
      category: 'Groceries',
      createdAt: now,
      updatedAt: now,
    }),
    db.transactions.add({
      id: crypto.randomUUID(),
      label: 'Lidl',
      amount: 3800,
      type: 'expense',
      date: dateInCurrentMonth(12),
      account_id: account1Id,
      budget_id: groceryId,
      category: 'Groceries',
      createdAt: now,
      updatedAt: now,
    }),
    db.transactions.add({
      id: crypto.randomUUID(),
      label: 'Monoprix',
      amount: 6200,
      type: 'expense',
      date: dateInCurrentMonth(18),
      account_id: account1Id,
      budget_id: groceryId,
      category: 'Groceries',
      createdAt: now,
      updatedAt: now,
    }),

    // Restaurants
    db.transactions.add({
      id: crypto.randomUUID(),
      label: 'Sushi Shop',
      amount: 3200,
      type: 'expense',
      date: dateInCurrentMonth(10),
      account_id: account1Id,
      budget_id: restaurantId,
      category: 'Dining',
      createdAt: now,
      updatedAt: now,
    }),
    db.transactions.add({
      id: crypto.randomUUID(),
      label: 'McDonald’s',
      amount: 1500,
      type: 'expense',
      date: dateInCurrentMonth(15),
      account_id: account1Id,
      budget_id: restaurantId,
      category: 'Dining',
      createdAt: now,
      updatedAt: now,
    }),
    db.transactions.add({
      id: crypto.randomUUID(),
      label: 'Italian Restaurant',
      amount: 4200,
      type: 'expense',
      date: dateInCurrentMonth(22),
      account_id: account1Id,
      budget_id: restaurantId,
      category: 'Dining',
      createdAt: now,
      updatedAt: now,
    }),

    // Savings transfer
    db.transactions.add({
      id: crypto.randomUUID(),
      label: 'Savings Transfer',
      amount: 20000,
      type: 'expense',
      date: dateInCurrentMonth(8),
      account_id: account1Id,
      createdAt: now,
      updatedAt: now,
    }),
  ])

  console.log('Db seeded')
}