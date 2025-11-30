<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Resources\Accounts\AccountResource;
use App\Filament\Admin\Resources\Accounts\Widgets\AccountBalanceBarChart;
use App\Filament\Admin\Resources\Accounts\Widgets\AccountPieDistribution;
use App\Filament\Admin\Resources\Budgets\BudgetResource;
use App\Filament\Admin\Resources\Transactions\Widgets\CurrentMonthTransactions;
use App\Models\Account;
use App\Models\Budget;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('/')
            ->default()
            ->login()
            ->unsavedChangesAlerts()
            ->colors($this->registerGlobalColors())
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->navigationItems($this->getNavigationItems())
            ->navigationGroups($this->getNavigationGroups())
            ->collapsibleNavigationGroups(true)
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                AccountPieDistribution::class,
                AccountBalanceBarChart::class,
                CurrentMonthTransactions::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup(__('filament.admin')),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function registerGlobalColors(): array
    {
        $colors = [
            'primary' => Color::Zinc,
        ];

        Account::query()
            ->select('id', 'color')
            ->whereNotNull('color')
            ->pluck('color', 'id')
            ->each(function (string $hexColor, int $accountId) use (&$colors): void {
                $colors["account-{$accountId}"] = $hexColor;
            });

        Budget::query()
            ->select('id', 'color')
            ->whereNotNull('color')
            ->pluck('color', 'id')
            ->each(function (string $hexColor, int $budgetId) use (&$colors): void {
                $colors["budget-{$budgetId}"] = $hexColor;
            });

        return $colors;
    }

    protected function getNavigationItems(): array
    {
        return [
            ...$this->buildNavigationItems(Account::all(), AccountResource::class, 'account.accounts'),
            ...$this->buildNavigationItems(Budget::all(), BudgetResource::class, 'budget.budgets'),
        ];
    }

    /**
     * Build navigation items for a collection of models
     *
     * @template TModel of Account|Budget
     *
     * @param  Collection<int, TModel>  $models  Collection of models to create navigation items for
     * @param  class-string<resource>  $resourceClass  Filament resource class (e.g., AccountResource::class)
     * @param  string  $groupTranslationKey  Translation key for the navigation group
     * @param  int  $startSort  Starting sort order (default: 2)
     * @return array Array of NavigationItem instances
     */
    protected function buildNavigationItems(Collection $models, string $resourceClass, string $groupTranslationKey, int $startSort = 2): array
    {
        $items = [];
        $sort = $startSort;

        /** @var Account|Budget $model */
        foreach ($models as $model) {
            $items[] = NavigationItem::make($model->label)
                ->url(fn () => $resourceClass::getUrl(
                    'view',
                    ['record' => $model->getKey()],
                    panel: 'admin'
                ))
                ->isActiveWhen(function () use ($model, $resourceClass) {
                    $currentUrl = request()->url();
                    $modelUrl = $resourceClass::getUrl(
                        'view',
                        ['record' => $model->getKey()],
                        panel: 'admin'
                    );

                    return $currentUrl === $modelUrl;
                })
                ->sort($sort++)
                ->group(fn () => __($groupTranslationKey));
        }

        return $items;
    }

    /**
     * Get all navigation groups with their configuration
     */
    protected function getNavigationGroups(): array
    {
        return [
            NavigationGroup::make()
                ->label(fn () => __('account.accounts'))
                ->icon(Heroicon::OutlinedWallet),
            NavigationGroup::make()
                ->label(fn () => __('budget.budgets'))
                ->icon(Heroicon::OutlinedChartPie),
            NavigationGroup::make()
                ->label(fn () => __('filament.admin')),
        ];
    }
}
