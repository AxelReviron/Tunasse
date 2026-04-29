export type Currency = 'EUR' | 'USD' | 'GBP' | 'BTC'

export type TableName = 'accounts' | 'budgets' | 'transactions'

export type Deletion = {
  id:        string
  tableName: TableName
  recordId:  string
  deletedAt: string
}

export type AccountType = 'checking' | 'savings' | 'investment'

export type RecurringUnit = 'day' | 'week' | 'month' | 'year'

export type TransactionType = 'income' | 'expense' | 'transfer'

export type Account = {
  id: string
  label: string
  type: AccountType
  balance: number
  color: string
  currency: Currency
  createdAt: string
  updatedAt: string
}

export type Budget = {
  id: string
  label: string
  amount: number
  currency: string
  color: string
  icon?: string
  createdAt: string
  updatedAt: string
}

export type Transaction = {
  id: string
  label: string
  amount: number
  type: TransactionType
  date: string
  location?: string
  category?: string
  icon?: string
  color?: string
  account_id: string
  budget_id?: string
  is_recurring?: boolean
  recurring_interval?: number
  recurring_unit?: RecurringUnit
  transfer_peer_id?: string
  to_account_id?: string
  createdAt: string
  updatedAt: string
}
