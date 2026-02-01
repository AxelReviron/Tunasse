<?php

namespace App\Filament\Admin\Resources\Transactions\Widgets;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RecurringIncomingExpensesOverview extends StatsOverviewWidget
{
    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $today = Carbon::now();
        $actualMonth = Carbon::now()->translatedFormat('F');

        $recurringExpenses = auth()->user()->transactions()
            ->whereIsRecurring(true)
            ->whereType(TransactionType::EXPENSE)
            ->get();

        $totalOccurrences = 0;
        $totalAmount = 0;
        $paidAmount = 0;

        foreach ($recurringExpenses as $transaction) {
            $occurrences = $transaction->getOccurrencesInRange($startOfMonth, $endOfMonth);
            $totalOccurrences += $occurrences;
            $totalAmount += $occurrences * $transaction->amount;

            // Calculate paid occurrences (up to today)
            $paidOccurrences = $transaction->getOccurrencesInRange($startOfMonth, $today);
            $paidAmount += $paidOccurrences * $transaction->amount;
        }

        // Calculate percentage paid, remaining amount and remaining percentage
        $paidPercentage = $totalAmount > 0 ? round(($paidAmount / $totalAmount) * 100, 1) : 0;
        $remainingAmount = $totalAmount - $paidAmount;
        $remainingPercentage = 100 - $paidPercentage;

        // TODO: Handle Currency

        return [
            Stat::make(__('transaction.widgets.recurring_expenses.title', ['month' => $actualMonth]), $totalOccurrences)
                ->extraAttributes(['class' => 'warning'])
                ->icon(Heroicon::OutlinedArrowPath),
            Stat::make(__('transaction.widgets.recurring_expenses.total_amount'), number_format($totalAmount, 2, ',', ' ').' €')
                ->extraAttributes(['class' => 'info'])
                ->icon(Heroicon::OutlinedCircleStack),
            Stat::make(__('transaction.widgets.recurring_expenses.remaining_amount', ['percentage' => $remainingPercentage.'%']), number_format($remainingAmount, 2, ',', ' ').' €')
                ->extraAttributes(['class' => 'danger'])
                ->icon(Heroicon::OutlinedExclamationTriangle),
            Stat::make(__('transaction.widgets.recurring_expenses.paid_amount', ['daily_percentages' => $paidPercentage.'%']), number_format($paidAmount, 2, ',', ' ').' €')
                ->extraAttributes(['class' => 'success'])
                ->icon(Heroicon::OutlinedCheckCircle),
        ];
    }
}
