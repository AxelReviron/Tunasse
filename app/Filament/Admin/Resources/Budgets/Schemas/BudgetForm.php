<?php

namespace App\Filament\Admin\Resources\Budgets\Schemas;

use App\Models\Currency;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make(__('budget.infos'))
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->schema(self::getInfosComponents()),

                    Step::make(__('budget.date'))
                        ->icon(Heroicon::OutlinedCalendarDateRange)
                        ->schema(self::getDateComponents()),
                ])
                    ->skippable(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Retourne tous les composants du formulaire (pour utilisation dans createOptionForm)
     *
     * @return array<\Filament\Schemas\Components\Component>
     */
    public static function getComponents(): array
    {
        return [
            ...self::getInfosComponents(),
            ...self::getDateComponents(),
        ];
    }

    /**
     * @return array<\Filament\Schemas\Components\Component>
     */
    public static function getInfosComponents(): array
    {
        $defaultCurrency = Currency::where('code', 'EUR')->first() ?
            Currency::where('code', 'EUR')->first()->getKey() :
            Currency::first() ?? new Currency([
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
        ]);

        return [
            TextInput::make('label')
                ->label(__('budget.label'))
                ->string()
                ->required()
                ->autofocus(),
            TextInput::make('amount')
                ->label(__('budget.amount'))
                ->required()
                ->numeric()
                ->minValue(0.01)
                ->step(0.01),
            Grid::make(2)->schema([
                ColorPicker::make('color')
                    ->label(__('budget.color'))
                    ->hexColor()
                    ->required(),
                Select::make('currency_id')
                    ->label(__('currency.currency'))
                    ->relationship('currency', 'name')
                    ->default($defaultCurrency)
                    ->required(),
            ]),
        ];
    }

    /**
     * @return array<\Filament\Schemas\Components\Component>
     */
    public static function getDateComponents(): array
    {
        return [
            Grid::make(2)->schema([
                DatePicker::make('start_date')
                    ->label(__('budget.start_date'))
                    ->date()
                    ->nullable(),
                DatePicker::make('end_date')
                    ->label(__('budget.end_date'))
                    ->date()
                    ->nullable()
                    ->afterOrEqual('start_date'),
            ]),
        ];
    }
}
