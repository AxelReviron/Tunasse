<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Resources\Accounts\Widgets\AccountBalanceBarChart;
use App\Filament\Admin\Resources\Accounts\Widgets\AccountPieDistribution;
use App\Filament\Admin\Resources\Budgets\Widgets\BudgetAmountBarChart;
use App\Filament\Admin\Resources\Budgets\Widgets\BudgetPieDistribution;
use App\Filament\Admin\Resources\Transactions\Widgets\CurrentMonthTransactions;
use App\Filament\Admin\Resources\Transactions\Widgets\RecurringIncomingExpensesOverview;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
        return $panel
            ->id('admin')
            ->path('/')
            ->default()
            ->login()
            ->unsavedChangesAlerts()
            ->colors([
                'primary' => Color::Zinc,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->collapsibleNavigationGroups(true)
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                RecurringIncomingExpensesOverview::class,
                CurrentMonthTransactions::class,
                AccountPieDistribution::class,
                BudgetPieDistribution::class,
                AccountBalanceBarChart::class,
                BudgetAmountBarChart::class,
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
}
