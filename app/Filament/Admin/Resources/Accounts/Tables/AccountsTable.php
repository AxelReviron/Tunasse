<?php

namespace App\Filament\Admin\Resources\Accounts\Tables;

use App\Filament\Admin\Actions\RecordActionsGroup;
use App\Filament\Admin\Columns\MetadataColumnGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereBelongsTo(auth()->user()))
            ->emptyStateHeading(__('account.no_accounts_yet'))
            ->emptyStateIcon(Heroicon::OutlinedWallet)
            ->columns([
                ColumnGroup::make(__('filament.infos'), [
                    TextColumn::make('label')
                        ->label(__('account.label'))
                        ->searchable(),
                    TextColumn::make('type')
                        ->label(__('account.type'))
                        ->color(fn ($record) => "account-{$record->getKey()}")
                        ->badge(),
                    TextColumn::make('balance')
                        ->label(__('account.balance'))
                        ->suffix(fn ($record) => ' '.$record->currency?->symbol)
                        ->numeric()
                        ->sortable(),
                    ColorColumn::make('color')
                        ->label(__('account.color'))
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('currency.name')
                        ->label(__('currency.currency'))
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('user.name')
                        ->label(__('user.user'))
                        ->searchable()
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
