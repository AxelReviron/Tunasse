<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

abstract class BarChartWidget extends ChartWidget
{
    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '150px';

    /**
     * The relationship on the user (e.g., 'accounts', 'budgets')
     */
    abstract protected function getRelationName(): string;

    /**
     * The field to use for chart values (e.g., 'balance', 'amount')
     */
    abstract protected function getValueField(): string;

    /**
     * The translation key for the description
     */
    abstract protected function getDescriptionKey(): string;

    /**
     * The translation key for the data label
     */
    abstract protected function getDataLabelKey(): string;

    /**
     * The translation key for the empty state
     */
    abstract protected function getEmptyStateKey(): string;

    public function getDescription(): string|Htmlable|null
    {
        return __($this->getDescriptionKey());
    }

    protected function getData(): array
    {
        $relationName = $this->getRelationName();
        $items = auth()->user()->{$relationName}()
            ->with('currency')
            ->get();

        if ($items->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => __($this->getEmptyStateLabelKey()),
                        'data' => [0],
                        'backgroundColor' => ['#9ca3af'],
                    ],
                ],
                'labels' => [__($this->getEmptyStateKey())],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => __($this->getDataLabelKey()),
                    'data' => $items->pluck($this->getValueField())->toArray(),
                    'backgroundColor' => $items->pluck('color')->toArray(),
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $items->pluck('label')->toArray(),
        ];
    }

    protected function getEmptyStateLabelKey(): string
    {
        return 'filament.no_data';
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
