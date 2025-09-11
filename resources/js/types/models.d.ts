export interface User {
    id: string;
    name: string;
    email: string;
    created_at: Date;
    update_at: Date;
}

export interface Currency {
    id: string;
    code: string;
    name: string;
    symbol: string;
    created_at: Date;
    update_at: Date;
}

export type AccountType = 'checking' | 'savings' | 'credits' | 'investment';

export interface Account {
    id: string;
    name: string;
    type: AccountType;
    balance: number;
    icon: string;
    currency_id: string;
    user_id: string;
    created_at: Date;
    update_at: Date;
}

export interface Budget {
    id: string;
    name: string;
    amount: number;
    start_date: Date;
    end_date: Date;
    icon: string;
    user_id: string;
    created_at: Date;
    update_at: Date;
}

export type RecurringUnitTransaction = 'day' | 'week' | 'month' | 'year';

export type TransactionType = 'income' | 'expense';

export interface Transaction {
    id: string;
    name: string;
    description: ?string;
    date: Date;
    is_recurring: boolean;
    recurring_interval: ?number;
    recurring_unit: RecurringUnitTransaction;
    location: string;
    amount: number;
    type: TransactionType;
    account_id: string;
    user_id: string;
    budget_id: string;
    created_at: Date;
    update_at: Date;
}
