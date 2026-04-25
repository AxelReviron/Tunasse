export type Currency = 'EUR' | 'USD' | 'GBP' | 'BTC'

export type AccountType = 'checking' | 'savings' | 'investment'

export type RecurringUnit = 'day' | 'week' | 'month' | 'year'

export type TransactionType = 'income' | 'expense'

export type Account = {
  id: number
  label: string
  iban: string
  type: AccountType
  balance: number
  currency: Currency
  color: string
}

export type Budget = {
  id: number
  label: string
  amount: number
  spent: number
  currency: string
  color: string
  icon?: string
}

export type Transaction = {
  id: number
  label: string
  amount: number
  type: TransactionType
  date: string
  location?: string
  category?: string
  account_id: number
  budget_id?: number
  is_recurring?: boolean
  recurring_unit?: RecurringUnit
}
