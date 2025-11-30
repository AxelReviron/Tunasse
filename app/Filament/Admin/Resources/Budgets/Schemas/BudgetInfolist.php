<?php

namespace App\Filament\Admin\Resources\Budgets\Schemas;

use App\Helper\DateHelper;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BudgetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->heading(__('budget.infos'))
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->schema([
                        TextEntry::make('label')
                            ->label(__('budget.label')),
                        TextEntry::make('amount')
                            ->label(__('budget.amount'))
                            ->numeric(),
                        ColorEntry::make('color')
                            ->label(__('budget.color')),
                        TextEntry::make('currency.name')
                            ->hidden()
                            ->label(__('currency.currency'))
                            ->placeholder('-'),
                        TextEntry::make('user.name')
                            ->label(__('user.user')),
                    ]),
                Section::make()
                    ->columns(1)
                    ->heading(__('budget.date'))
                    ->icon(Heroicon::OutlinedCalendarDateRange)
                    ->schema([
                        TextEntry::make('start_date')
                            ->label(__('budget.start_date'))
                            ->formatStateUsing(fn ($state) => DateHelper::formatDate($state, false)),
                        TextEntry::make('end_date')
                            ->label(__('budget.end_date'))
                            ->formatStateUsing(fn ($state) => DateHelper::formatDate($state, false)),
                    ]),
            ]);
    }
}
