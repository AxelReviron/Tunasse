<?php

namespace App\Filament\Admin\Resources\Transactions\Widgets;

use App\Enums\AccountType;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class CurrentMonthTransactions extends ChartWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    public ?string $filter = null;

    public function getDescription(): string|Htmlable|null
    {
        return __('transaction.widgets.current_month');
    }

    protected function getFilters(): ?array
    {
        return auth()->user()->accounts()
            ->orderByRaw("type = '".AccountType::CHECKING->value."' DESC")
            ->orderBy('label')
            ->pluck('label', 'id')
            ->toArray();
    }

    public function getFilter(): ?string
    {
        if ($this->filter === null) {
            $defaultAccount = auth()->user()->accounts()
                ->where('type', AccountType::CHECKING)
                ->first();

            $this->filter = $defaultAccount?->getKey() ?? auth()->user()->accounts()->first()?->getKey();
        }

        return $this->filter;
    }

    protected function getData(): array
    {
        $accountId = $this->getFilter();

        if (! $accountId) {
            return [
                'datasets' => [
                    [
                        'label' => __('transaction.widgets.no_data'),
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ];
        }

        $account = auth()->user()->accounts()->find($accountId);

        if (! $account) {
            return [
                'datasets' => [
                    [
                        'label' => __('transaction.widgets.no_data'),
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ];
        }

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $transactions = $account->transactions()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date')
            ->get();

        $incomeByDay = [];
        $expenseByDay = [];
        $incomePointRadius = [];
        $expensePointRadius = [];
        $labels = [];

        for ($date = $startOfMonth->copy(); $date <= $endOfMonth; $date->addDay()) {
            $dayKey = $date->format('Y-m-d');
            $labels[] = $date->format('d');
            $incomeByDay[$dayKey] = 0;
            $expenseByDay[$dayKey] = 0;
        }

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $dayKey = Carbon::parse($transaction->date)->format('Y-m-d');

            if ($transaction->type === TransactionType::INCOME) {
                $incomeByDay[$dayKey] = ($incomeByDay[$dayKey] ?? 0) + abs($transaction->amount);
            } else {
                $expenseByDay[$dayKey] = ($expenseByDay[$dayKey] ?? 0) + abs($transaction->amount);
            }
        }

        foreach ($incomeByDay as $amount) {
            $incomePointRadius[] = $amount > 0.0 ? 4 : 0;
        }

        foreach ($expenseByDay as $amount) {
            $expensePointRadius[] = $amount > 0.0 ? 4 : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => __('transaction.income'),
                    'data' => array_values($incomeByDay),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => $incomePointRadius,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => __('transaction.expense'),
                    'data' => array_values($expenseByDay),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => $expensePointRadius,
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
