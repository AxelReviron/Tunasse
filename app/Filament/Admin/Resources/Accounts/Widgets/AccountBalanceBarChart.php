<?php

namespace App\Filament\Admin\Resources\Accounts\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class AccountBalanceBarChart extends ChartWidget
{
    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '150px';

    public function getDescription(): string|Htmlable|null
    {
        return __('account.widgets.balance_distribution');
    }

    protected function getData(): array
    {
        $accounts = auth()->user()->accounts()
            ->with('currency')
            ->get();

        if ($accounts->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => __('account.widgets.no_data'),
                        'data' => [0],
                        'backgroundColor' => ['#9ca3af'], // gris
                    ],
                ],
                'labels' => [__('account.widgets.total_stats_no_account')],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => __('account.balance'),
                    'data' => $accounts->pluck('balance')->toArray(),
                    'backgroundColor' => $accounts->pluck('color')->toArray(),
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $accounts->pluck('label')->toArray(),
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
                    'display' => false,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
