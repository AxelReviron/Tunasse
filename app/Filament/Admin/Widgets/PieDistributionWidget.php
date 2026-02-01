<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

abstract class PieDistributionWidget extends ChartWidget
{
    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '150px';

    /**
     * Relationship from user (ex: 'accounts', 'budgets')
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
                        'data' => [0],
                    ],
                ],
                'labels' => [__($this->getEmptyStateKey())],
            ];
        }

        return [
            'datasets' => [
                [
                    'data' => $items->pluck($this->getValueField())->toArray(),
                    'backgroundColor' => $items->pluck('color')->toArray(),
                ],
            ],
            'labels' => $items->pluck('label')->toArray(),
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
