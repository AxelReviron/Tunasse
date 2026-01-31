<?php

namespace App\Filament\Admin\Resources\Transactions\Schemas;

use App\Enums\RecurringTransactionUnit;
use App\Enums\TransactionType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        /*
         * TODO: Rendre recurring_interval et recurring_unit requis quand is_recurring est true
         *  - Valider que amount > 0
         *  - Widget pour voir le total des dépenses/revenus du mois
         *  - Graphique des transactions par catégorie/budget
         *  - Solde total de tous les comptes
         *  - Empecher la création si il n'y a aucun compte
         */
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->heading(__('transaction.infos'))
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->schema([
                        TextInput::make('label')
                            ->label(__('transaction.label'))
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label(__('transaction.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                        DatePicker::make('date')
                            ->label(__('transaction.date'))
                            ->required(),
                        TextInput::make('location')
                            ->label(__('transaction.location')),
                        Select::make('type')
                            ->label(__('transaction.type'))
                            ->options(TransactionType::class)
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Grid::make()
                    ->columns(2)
                    ->schema([
                        Section::make()
                            ->columnSpanFull()
                            ->heading(__('transaction.amount_details'))
                            ->icon(Heroicon::OutlinedBanknotes)
                            ->schema([
                                TextInput::make('amount')
                                    ->label(__('transaction.amount'))
                                    ->required()
                                    ->numeric(),
                                Select::make('account_id')
                                    ->label(__('transaction.account'))
                                    ->relationship('account', 'label', fn ($query) => $query->whereBelongsTo(auth()->user()))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('budget_id')
                                    ->label(__('transaction.budget'))
                                    ->relationship('budget', 'label', fn ($query) => $query->whereBelongsTo(auth()->user()))
                                    ->searchable()
                                    ->preload(),
                            ]),
                        Section::make()
                            ->columnSpanFull()
                            ->heading(__('transaction.recurrence'))
                            ->icon(Heroicon::OutlinedArrowPath)
                            ->schema([
                                Toggle::make('is_recurring')
                                    ->label(__('transaction.is_recurring'))
                                    ->live()
                                    ->default(false),
                                TextInput::make('recurring_interval')
                                    ->label(__('transaction.recurring_interval'))
                                    ->numeric()
                                    ->minValue(1)
                                    ->visible(fn ($get) => $get('is_recurring')),
                                Select::make('recurring_unit')
                                    ->label(__('transaction.recurring_unit'))
                                    ->options(RecurringTransactionUnit::class)
                                    ->visible(fn ($get) => $get('is_recurring')),
                            ]),
                    ]),
                Select::make('user_id')
                    ->label(__('transaction.user'))
                    ->relationship('user', 'name')
                    ->hidden()
                    ->dehydrated()
                    ->dehydrateStateUsing(fn () => auth()->user()->getKey())
                    ->default(fn () => auth()->id())
                    ->required(),
            ]);
    }
}
