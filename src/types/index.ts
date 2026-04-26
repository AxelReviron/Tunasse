export type Currency = 'EUR' | 'USD' | 'GBP' | 'BTC'

export type AccountType = 'checking' | 'savings' | 'investment'

export type RecurringUnit = 'day' | 'week' | 'month' | 'year'

export type TransactionType = 'income' | 'expense' | 'transfer'

export type Account = {
  id: number
  label: string
  type: AccountType
  balance: number
  color: string
  currency: Currency
}

export type Budget = {
  id: number
  label: string
  amount: number
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
  icon?: string
  color?: string
  account_id: number
  budget_id?: number
  is_recurring?: boolean
  recurring_interval?: number
  recurring_unit?: RecurringUnit
  transfer_peer_id?: number
  to_account_id?: number
}
