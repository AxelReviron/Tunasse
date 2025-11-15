<?php

namespace App\Filament\Admin\Resources\Accounts\Schemas;

use App\Enums\AccountType;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('label')
                            ->label(__('account.label'))
                            ->string()
                            ->required(),
                        Select::make('type')
                            ->options(AccountType::class)
                            ->label(__('account.type'))
                            ->required(),
                        TextInput::make('balance')
                            ->label(__('account.balance'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        ColorPicker::make('color')
                            ->label(__('account.color'))
                            ->hexColor()
                            ->required(),
                        Select::make('currency_id')
                            ->label(__('currency.currency'))
                            ->relationship('currency', 'name'),
                        Select::make('user_id')
                            ->label(__('user.user'))
                            ->relationship('user', 'name')
                            ->hidden()
                            ->default(fn () => auth()->id())
                            ->required(),
                    ]),
            ]);
    }
}
