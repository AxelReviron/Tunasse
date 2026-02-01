<?php

namespace App\Filament\Admin\Resources\Budgets;

use App\Filament\Admin\Resources\Budgets\Pages\CreateBudget;
use App\Filament\Admin\Resources\Budgets\Pages\EditBudget;
use App\Filament\Admin\Resources\Budgets\Pages\ListBudgets;
use App\Filament\Admin\Resources\Budgets\Pages\ViewBudget;
use App\Filament\Admin\Resources\Budgets\RelationManagers\TransactionsRelationManager;
use App\Filament\Admin\Resources\Budgets\Schemas\BudgetForm;
use App\Filament\Admin\Resources\Budgets\Schemas\BudgetInfolist;
use App\Filament\Admin\Resources\Budgets\Tables\BudgetsTable;
use App\Models\Budget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartPie;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return BudgetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BudgetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BudgetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBudgets::route('/'),
            'create' => CreateBudget::route('/create'),
            'view' => ViewBudget::route('/{record}'),
            'edit' => EditBudget::route('/{record}/edit'),
        ];
    }

    // Get only user related records
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->user()->getKey());
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPluralLabel(): ?string
    {
        return __('budget.budgets');
    }

    public static function getLabel(): ?string
    {
        return __('budget.budget');
    }
}
