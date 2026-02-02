<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AccountBalanceCalculator
{
    /**
     * Calculate the account balance, optionally at a specific date.
     *
     * @param  Account  $account  The account to calculate balance for
     * @param  Carbon|null  $atDate  If provided, calculate balance up to and including this date
     * @return float Balance in major currency units
     */
    public function getBalance(Account $account, ?Carbon $atDate = null): float
    {
        $query = $account->transactions();

        if ($atDate !== null) {
            $query->where('date', '<=', $atDate);
        }

        // SUM returns minor units (raw database values)
        $incomeMinor = (clone $query)
            ->where('type', TransactionType::INCOME)
            ->sum('amount');

        $expenseMinor = (clone $query)
            ->where('type', TransactionType::EXPENSE)
            ->sum('amount');

        $balanceMinor = (int) $incomeMinor - (int) $expenseMinor;

        // Convert to major units
        $decimals = $account->currency?->decimal_places ?? 2;

        return $balanceMinor / (10 ** $decimals);
    }

    /**
     * Get the daily balance evolution for an account within a date range.
     *
     * @return array{values: array<float>, labels: array<string>, transactions: array<array<string>>}
     */
    public function getBalanceEvolution(Account $account, Carbon $start, Carbon $end): array
    {
        $balanceBeforeStart = $this->getBalance($account, $start->copy()->subDay());

        $transactions = $account->transactions()
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get(['date', 'amount', 'type', 'label']);

        $transactionsByDay = $this->groupTransactionsByDay($transactions);

        return $this->buildDailyEvolution($transactionsByDay, $balanceBeforeStart, $start, $end);
    }

    /**
     * @return Collection<string, Collection>
     */
    private function groupTransactionsByDay(Collection $transactions): Collection
    {
        return $transactions->groupBy(
            fn ($transaction) => Carbon::parse($transaction->date)->format('Y-m-d')
        );
    }

    /**
     * @return array{values: array<float>, labels: array<string>, transactions: array<array<string>>}
     */
    private function buildDailyEvolution(
        Collection $transactionsByDay,
        float $startingBalance,
        Carbon $start,
        Carbon $end
    ): array {
        $values = [];
        $labels = [];
        $transactions = [];
        $runningBalance = $startingBalance;

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $dayKey = $date->format('Y-m-d');
            $labels[] = $date->format('d');

            $runningBalance += $this->getDayBalanceChange($transactionsByDay, $dayKey);
            $values[] = round($runningBalance, 2);
            $transactions[] = $this->getDayTransactionLabels($transactionsByDay, $dayKey);
        }

        return compact('values', 'labels', 'transactions');
    }

    /**
     * Get transaction labels for a specific day.
     *
     * @return array<string>
     */
    private function getDayTransactionLabels(Collection $transactionsByDay, string $dayKey): array
    {
        if (! isset($transactionsByDay[$dayKey])) {
            return [];
        }

        return $transactionsByDay[$dayKey]
            ->map(fn ($t) => ($t->type === TransactionType::INCOME ? '+' : '-').' '.$t->label)
            ->values()
            ->toArray();
    }

    /**
     * Calculate the total balance change for a specific day.
     */
    private function getDayBalanceChange(Collection $transactionsByDay, string $dayKey): float
    {
        if (! isset($transactionsByDay[$dayKey])) {
            return 0;
        }

        return $transactionsByDay[$dayKey]->sum(
            fn ($transaction) => $transaction->type === TransactionType::INCOME
                ? $transaction->amount
                : -$transaction->amount
        );
    }
}
