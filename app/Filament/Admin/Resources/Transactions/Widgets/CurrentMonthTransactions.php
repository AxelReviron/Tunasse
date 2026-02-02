<?php

namespace App\Filament\Admin\Resources\Transactions\Widgets;

use App\Enums\AccountType;
use App\Models\Account;
use App\Services\AccountBalanceCalculator;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class CurrentMonthTransactions extends ChartWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    public ?string $filter = null;

    /**
     * @var array<array<string>>
     */
    public array $transactionLabels = [];

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
        $account = $this->getSelectedAccount();

        if (! $account) {
            return $this->getEmptyDataset();
        }

        $calculator = app(AccountBalanceCalculator::class);
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $balanceEvolution = $calculator->getBalanceEvolution($account, $startOfMonth, $endOfMonth);

        $this->transactionLabels = $balanceEvolution['transactions'];

        $color = $account->color ?? '#3b82f6';
        $currencySymbol = $account->currency?->symbol ?? '€';

        $pointRadius = array_map(
            fn (array $transactions) => count($transactions) > 0 ? 4 : 0,
            $balanceEvolution['transactions']
        );

        return [
            'datasets' => [
                [
                    'label' => __('account.balance'),
                    'data' => $balanceEvolution['values'],
                    'borderColor' => $color,
                    'backgroundColor' => $this->hexToRgba($color, 0.1),
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => $pointRadius,
                    'pointHoverRadius' => 6,
                    'transactions' => $balanceEvolution['transactions'],
                ],
            ],
            'labels' => $balanceEvolution['labels'],
            'currencySymbol' => $currencySymbol,
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                    },
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            afterBody: function(context) {
                                const dataIndex = context[0].dataIndex;
                                const transactions = context[0].dataset.transactions;
                                if (transactions && transactions[dataIndex] && transactions[dataIndex].length > 0) {
                                    return ['', 'Transactions:', ...transactions[dataIndex]];
                                }
                                return [];
                            },
                        },
                    },
                },
            }
        JS);
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getSelectedAccount(): ?Account
    {
        $accountId = $this->getFilter();

        if (! $accountId) {
            return null;
        }

        return auth()->user()->accounts()->with('currency')->find($accountId);
    }

    /**
     * @return array{datasets: array, labels: array}
     */
    private function getEmptyDataset(): array
    {
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

    /**
     * Convert hex color to rgba string.
     */
    private function hexToRgba(string $hex, float $alpha): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }
}
