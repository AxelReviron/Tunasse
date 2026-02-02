<?php

namespace App\Filament\Admin\Resources\Accounts\Schemas;

use App\Enums\AccountType;
use App\Models\Currency;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        $defaultCurrency = Currency::where('code', 'EUR')->first() ?
            Currency::where('code', 'EUR')->first()->getKey() :
            Currency::first() ?? new Currency([
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
        ]);

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
                            ->belowContent([
                                Icon::make(Heroicon::OutlinedQuestionMarkCircle),
                                __('account.balance_helper')
                            ])
                            ->disabled()
                            ->required()
                            ->numeric()
                            ->default(0),
                        ColorPicker::make('color')
                            ->label(__('account.color'))
                            ->belowContent([
                                Icon::make(Heroicon::OutlinedQuestionMarkCircle),
                                __('account.color_helper')
                            ])
                            ->hexColor()
                            ->required(),
                        Select::make('currency_id')
                            ->label(__('currency.currency'))
                            ->default($defaultCurrency)
                            ->relationship('currency', 'name')
                            ->required(),
                    ]),
            ]);
    }
}
