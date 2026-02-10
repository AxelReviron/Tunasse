<?php

namespace App\Filament\Admin\Resources\Transactions\Schemas;

use App\Enums\TransactionType;
use App\Filament\Infolists\Components\ColoredBadgeEntry;
use App\Helper\DateHelper;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->heading(__('transaction.infos'))
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->schema([
                        TextEntry::make('label')
                            ->label(__('transaction.label'))
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->label(__('transaction.description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('date')
                            ->label(__('transaction.date'))
                            ->formatStateUsing(fn ($state) => DateHelper::formatDate($state, false)),
                        TextEntry::make('location')
                            ->label(__('transaction.location'))
                            ->placeholder('-'),
                        TextEntry::make('type')
                            ->label(__('transaction.type'))
                            ->badge()
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
                                TextEntry::make('amount')
                                    ->label(__('transaction.amount'))
                                    ->numeric()
                                    ->suffix(fn ($record) => ' '.$record->account?->currency?->symbol)
                                    ->prefix(fn ($record) => $record->type === TransactionType::EXPENSE ? '- ' : '+ ')
                                    ->color(fn ($record) => $record->type === TransactionType::EXPENSE ? 'danger' : 'success')
                                    ->formatStateUsing(function ($state, $record) {// TODO: Use this for budget and account?
                                        $decimals = $record->account?->currency?->decimal_places;
                                        return $state / (10 ** $decimals);
                                    }),
                                ColoredBadgeEntry::make('account.label')
                                    ->label(__('transaction.account'))
                                    ->color(fn ($record) => $record->account ? $record->account->color : null),
                                ColoredBadgeEntry::make('budget.label')
                                    ->label(__('transaction.budget'))
                                    ->color(fn ($record) => $record->budget ? $record->budget->color : null)
                                    ->placeholder('-'),
                            ]),
                        Section::make()
                            ->columnSpanFull()
                            ->heading(__('transaction.recurrence'))
                            ->icon(Heroicon::OutlinedArrowPath)
                            ->schema([
                                IconEntry::make('is_recurring')
                                    ->label(__('transaction.is_recurring'))
                                    ->boolean(),
                                TextEntry::make('recurrence_label')
                                    ->label(__('transaction.frequency'))
                                    ->placeholder('-'),
                            ]),
                    ]),

            ]);
    }
}
