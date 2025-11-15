<?php

namespace App\Filament\Admin\Resources\Accounts\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AccountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(1)
                    ->schema([
                        TextEntry::make('label')
                            ->label(__('account.label')),
                        TextEntry::make('type')
                            ->label(__('account.type'))
                            ->badge(),
                    ]),
                Section::make()
                    ->columns(1)
                    ->schema([
                        TextEntry::make('balance')
                            ->label(__('account.balance'))
                            ->suffix(fn ($record) => ' '.$record->currency?->symbol)
                            ->numeric(),
                        ColorEntry::make('color')
                            ->label(__('account.color')),
                    ]),
                TextEntry::make('currency.name')
                    ->hidden()
                    ->label(__('currency.currency'))
                    ->placeholder('-'),
                TextEntry::make('user.name')
                    ->label(__('user.user'))
                    ->hidden(),
            ]);
    }
}
