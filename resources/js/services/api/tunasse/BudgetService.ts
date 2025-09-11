import { ApiService } from '@/services/api/tunasse/ApiService';
import { Budget } from '@/types/models';

export class BudgetService extends ApiService {
    constructor() {
        super('/api/budgets');
    }

    async getUserBudgets(userId: string): Promise<Budget[]> {
        return this.search<Budget[]>({
            filters: {
                user_id: {
                    eq: userId,
                }
            }
        });
    }
}
