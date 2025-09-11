import { ApiService } from '@/services/api/tunasse/ApiService';
import { Transaction } from '@/types/models';

export class TransactionService extends ApiService {
    constructor() {
        super('/api/transactions')
    }

    async getUserMonthlyRecurringExpenseSinceToday(userId: string): Promise<Transaction[]> {
        const now = new Date();

        // Date of the current day
        const today = now.toISOString().split('T')[0];

        // Last day of the month
        const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0)
            .toISOString()
            .split('T')[0];

        return this.search<Transaction>({
            filters: {
                user_id: {
                    eq: userId
                },
                is_recurring: { eq: true },
                recurring_interval: { eq: 1 },
                recurring_unit: { eq: 'month' },
                date: {
                    gte: today,
                    lte: endOfMonth
                },
                type: {
                    eq: 'expense',
                },
            }
        })
    }

    async getUserCurrentMonthTransactions(userId: string): Promise<Transaction[]> {
        const now = new Date();

        // Start of month (YYYY-MM-DD)
        const start = new Date(now.getFullYear(), now.getMonth(), 1)
            .toISOString()
            .split("T")[0];

        // End of month (YYYY-MM-DD)
        const today = now.toISOString().split('T')[0];

        return this.search<Transaction>({
            filters: {
                user_id: {
                    eq: userId
                },
                date: {
                    between: [start, today]
                } },
            sort: 'date',
            direction: 'asc',
        });
    }
}
