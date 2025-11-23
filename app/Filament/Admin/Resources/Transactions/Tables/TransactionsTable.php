<?php

namespace App\Filament\Admin\Resources\Transactions\Tables;

use App\Enums\TransactionType;
use App\Filament\Admin\Actions\RecordActionsGroup;
use App\Filament\Admin\Columns\MetadataColumnGroup;
use App\Helper\DateHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereBelongsTo(auth()->user()))
            ->defaultSort('date')
            ->columns([
                ColumnGroup::make(__('filament.infos'), [
                    TextColumn::make('label')
                        ->label(__('transaction.label'))
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('date')
                        ->label(__('transaction.date'))
                        ->formatStateUsing(fn ($state) => DateHelper::formatDate($state, false))
                        ->sortable(),
                    TextColumn::make('amount')
                        ->label(__('transaction.amount'))
                        ->suffix(fn ($record) => ' '.$record->account?->currency?->symbol)
                        ->prefix(fn ($record) => $record->type === TransactionType::EXPENSE ? '- ' : '+ ')
                        ->color(fn ($record) => $record->type === TransactionType::EXPENSE ? 'danger' : 'success')
                        ->numeric()
                        ->sortable(),
                    TextColumn::make('recurrence_label')
                        ->label(__('transaction.is_recurring'))
                        ->sortable(),
                    TextColumn::make('location')
                        ->label(__('transaction.location'))
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('description')
                        ->label(__('transaction.description'))
                        ->limit(20)
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('account.label')
                        ->label(__('transaction.account'))
                        ->color(fn ($record) => $record->account ? "account-{$record->account->getKey()}" : null)
                        ->badge()
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('budget.label')
                        ->label(__('transaction.budget'))
                        ->color(fn ($record) => $record->budget ? "budget-{$record->budget->getKey()}" : null)
                        ->badge()
                        ->searchable(),
                    TextColumn::make('type')
                        ->label(__('transaction.type'))
                        ->color(fn ($state) => $state === TransactionType::EXPENSE ? 'danger' : 'success')
                        ->badge()
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('user.name')
                        ->label(__('transaction.user'))
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
                MetadataColumnGroup::make(__('filament.metadata')),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions(RecordActionsGroup::make([]))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
