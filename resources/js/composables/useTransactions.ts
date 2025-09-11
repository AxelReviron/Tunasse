import { Transaction } from '@/types/models';

export function useTransactions() {

    function calculateTotalAmountTransactions(transactions: Transaction[]): number {
        return transactions.reduce(
            (sum: number, tx: any) => sum + Number(tx.amount),
            0
        );
    }

    return {
        calculateTotalAmountTransactions,
    }
}
