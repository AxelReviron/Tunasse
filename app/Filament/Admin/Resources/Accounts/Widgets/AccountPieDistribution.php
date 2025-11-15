<?php

namespace App\Filament\Admin\Resources\Accounts\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class AccountPieDistribution extends ChartWidget
{
    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '150px';

    public function getDescription(): string|Htmlable|null
    {
        return __('account.widgets.total_stats');
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
                        'data' => [0],
                    ],
                ],
                'labels' => [__('account.widgets.total_stats_no_account')],
            ];
        }

        // TODO: Ajouter les devises
        return [
            'datasets' => [
                [
                    'data' => $accounts->pluck('balance')->toArray(),
                    'backgroundColor' => $accounts->pluck('color')->toArray(),
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
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels' => [
                        'padding' => 20,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
