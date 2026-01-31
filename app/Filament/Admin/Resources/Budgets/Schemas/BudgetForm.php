<?php

namespace App\Filament\Admin\Resources\Budgets\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(1)
                    ->heading(__('budget.infos'))
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->schema([
                        TextInput::make('label')
                            ->label(__('budget.label'))
                            ->string()
                            ->required(),
                        TextInput::make('amount')
                            ->label(__('budget.amount'))
                            ->required()
                            ->numeric(),
                        ColorPicker::make('color')
                            ->label(__('budget.color'))
                            ->hexColor()
                            ->required(),
                        Select::make('currency_id')
                            ->label(__('currency.currency'))
                            ->relationship('currency', 'name'),
                    ]),
                Section::make()
                    ->columns(1)
                    ->heading(__('budget.date'))
                    ->icon(Heroicon::OutlinedCalendarDateRange)
                    ->schema([
                        DatePicker::make('start_date')
                            ->label(__('budget.start_date'))
                            ->date()
                            ->nullable(),
                        DatePicker::make('end_date')
                            ->label(__('budget.end_date'))
                            ->date()
                            ->nullable(),
                    ]),
            ]);
    }
}
