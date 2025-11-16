<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Resources\Accounts\AccountResource;
use App\Filament\Admin\Resources\Accounts\Widgets\AccountBalanceBarChart;
use App\Filament\Admin\Resources\Accounts\Widgets\AccountPieDistribution;
use App\Filament\Admin\Resources\Budgets\BudgetResource;
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
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $navItems = [];
        $sort = 2;

        // Accounts
        foreach (Account::all() as $account) {
            $navItems[] = NavigationItem::make($account->label)
                ->url(fn () => AccountResource::getUrl(
                    'view',
                    ['record' => $account->getKey()],
                    panel: 'admin'
                ))
                ->isActiveWhen(function () use ($account) {
                    return request()->route('record') == $account->getKey();
                })
                ->sort($sort++)
                ->group(__('account.accounts'));
        }

        return $panel
            ->id('admin')
            ->path('/')
            ->default()
            ->login()
            ->colors([
                'primary' => Color::Green,
            ])
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

    protected function getNavigationItems(): array
    {
        return [
            ...$this->getAccountNavigationItems(),
            ...$this->getBudgetNavigationItems(),
            // ...$this->getTransactionNavigationItems(),
        ];
    }

    /**
     * Get navigation items for accounts
     */
    protected function getAccountNavigationItems(): array
    {
        $items = [];
        $sort = 2;

        foreach (Account::all() as $account) {
            $items[] = NavigationItem::make($account->label)
                ->url(fn () => AccountResource::getUrl(
                    'view',
                    ['record' => $account->getKey()],
                    panel: 'admin'
                ))
                ->isActiveWhen(fn () => request()->route('record') == $account->getKey())
                ->sort($sort++)
                ->group(__('account.accounts'));
        }

        return $items;
    }

    /**
     * Get navigation items for budget
     */
    protected function getBudgetNavigationItems(): array
    {
        $items = [];
        $sort = 2;

        foreach (Budget::all() as $budget) {
            $items[] = NavigationItem::make($budget->label)
                ->url(fn () => BudgetResource::getUrl(
                    'view',
                    ['record' => $budget->getKey()],
                    panel: 'admin'
                ))
                ->isActiveWhen(fn () => request()->route('record') == $budget->getKey())
                ->sort($sort++)
                ->group(__('budget.budgets'));
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
                ->label(__('account.accounts'))
                ->icon(Heroicon::OutlinedWallet),
            NavigationGroup::make()
                ->label(__('budget.budgets'))
                ->icon(Heroicon::OutlinedChartPie),
            NavigationGroup::make()
                ->label(__('filament.admin')),
        ];
    }
}
