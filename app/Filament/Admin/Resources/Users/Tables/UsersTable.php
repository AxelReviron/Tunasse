<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Filament\Admin\Actions\RecordActionsGroup;
use App\Filament\Admin\Columns\MetadataColumnGroup;
use App\Helper\DateHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColumnGroup::make(__('filament.infos'), [
                    TextColumn::make('name')
                        ->label(__('user.name'))
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('email')
                        ->label(__('user.email'))
                        ->searchable(),
                    TextColumn::make('roles.name')
                        ->label(__('filament.roles'))
                        ->columnSpanFull(),
                    TextColumn::make('email_verified_at')
                        ->label(__('user.email_verified_at'))
                        ->formatStateUsing(fn ($state) => DateHelper::formatDate($state))
                        ->sortable(),
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
                ]),
            ]);
    }
}
