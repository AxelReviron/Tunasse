<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.infos'))
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('user.name'))
                            ->placeholder(__('user.name_placeholder'))
                            ->columnSpanFull()
                            ->string()
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('email')
                            ->label(__('user.email'))
                            ->placeholder(__('user.email_placeholder'))
                            ->columnSpanFull()
                            ->email()
                            ->required(),
                        TextInput::make('password')
                            ->label(__('user.password'))
                            ->placeholder(__('user.password_placeholder'))
                            ->columnSpanFull()
                            ->password()
                            ->revealable()
                            ->confirmed()
                            ->required()
                            ->rules([
                                Password::min(8)
                                    ->letters()
                                    ->mixedCase()
                                    ->numbers()
                                    ->symbols()
                                    ->uncompromised(),
                            ]),
                        TextInput::make('password_confirmation')
                            ->label(__('user.password_confirmation'))
                            ->placeholder(__('user.password_placeholder'))
                            ->columnSpanFull()
                            ->password()
                            ->revealable()
                            ->required(),
                        Select::make('roles')
                            ->label(__('filament.roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }
}
