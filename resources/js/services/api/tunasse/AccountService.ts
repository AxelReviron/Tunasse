import { ApiService } from '@/services/api/tunasse/ApiService';
import { Account } from '@/types/models';

export class AccountService extends ApiService {
    constructor() {
        super('/api/accounts');
    }

    async getUserAccounts(userId: string): Promise<Account[]> {
        return this.search<Account[]>({
            filters: {
                user_id: {
                    eq: userId
                }
            },
            sort: 'date',
            direction: 'asc'
        });
    }
}
