<?php

namespace App\Filament\Admin\Resources\Transactions\Schemas;

use App\Enums\RecurringTransactionUnit;
use App\Enums\TransactionType;
use App\Filament\Admin\Resources\Accounts\AccountResource;
use App\Filament\Admin\Resources\Budgets\Schemas\BudgetForm;
use App\Models\Account;
use App\Models\Budget;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        $hasAccounts = Account::query()
            ->whereBelongsTo(auth()->user())
            ->exists();

        if (! $hasAccounts) {
            return $schema->components([
                EmptyState::make(__('transaction.no_account_title'))
                    ->columnSpanFull()
                    ->description(__('transaction.no_account_description'))
                    ->icon(Heroicon::OutlinedWallet)
                    ->footer([
                        Action::make('createAccount')
                            ->label(__('account.create'))
                            ->icon(Heroicon::Plus)
                            ->url(AccountResource::getUrl('create')),
                    ]),
            ]);
        }

        return $schema
            ->components([
                Wizard::make([
                    Step::make(__('transaction.infos'))
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->schema([
                            TextInput::make('label')
                                ->label(__('transaction.label'))
                                ->required()
                                ->autofocus(),
                            Textarea::make('description')
                                ->label(__('transaction.description'))
                                ->rows(3),
                            Grid::make(2)->schema([
                                DatePicker::make('date')
                                    ->label(__('transaction.date'))
                                    ->required(),
                                TextInput::make('location')
                                    ->label(__('transaction.location')),
                            ]),
                            ToggleButtons::make('type')
                                ->label(__('transaction.type'))
                                ->options(TransactionType::class)
                                ->icons([
                                    TransactionType::INCOME->value => Heroicon::ArrowTrendingUp,
                                    TransactionType::EXPENSE->value => Heroicon::ArrowTrendingDown,
                                ])
                                ->colors([
                                    TransactionType::INCOME->value => 'success',
                                    TransactionType::EXPENSE->value => 'danger',
                                ])
                                ->inline()
                                ->required(),
                        ]),

                    Step::make(__('transaction.amount_details'))
                        ->icon(Heroicon::OutlinedBanknotes)
                        ->schema([
                            TextInput::make('amount')
                                ->label(__('transaction.amount'))
                                ->required()
                                ->numeric()
                                ->minValue(0.01)
                                ->step(0.01)
                                ->autofocus(),
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
                                ->preload()
                                ->createOptionForm(BudgetForm::getComponents())
                                ->createOptionUsing(function (array $data): int {
                                    $data['user_id'] = auth()->user()->getKey();

                                    return Budget::create($data)->getKey();
                                }),
                        ]),

                    Step::make(__('transaction.recurrence'))
                        ->icon(Heroicon::OutlinedArrowPath)
                        ->schema([
                            Toggle::make('is_recurring')
                                ->label(__('transaction.is_recurring'))
                                ->live()
                                ->default(false),
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('recurring_interval')
                                        ->label(__('transaction.recurring_interval'))
                                        ->numeric()
                                        ->minValue(1)
                                        ->visible(fn ($get) => $get('is_recurring'))
                                        ->required(fn ($get) => $get('is_recurring')),
                                    Select::make('recurring_unit')
                                        ->label(__('transaction.recurring_unit'))
                                        ->options(RecurringTransactionUnit::class)
                                        ->visible(fn ($get) => $get('is_recurring'))
                                        ->required(fn ($get) => $get('is_recurring')),
                                ]),
                        ]),
                ])
                    ->skippable(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                    ->columnSpanFull(),
            ]);
    }
}
