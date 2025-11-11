<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Helper\DateHelper;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.infos'))
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('user.name'))
                            ->columnSpanFull(),
                        TextEntry::make('email')
                            ->label(__('user.email'))
                            ->columnSpanFull(),
                        TextEntry::make('email_verified_at')
                            ->label(__('user.email_verified_at'))
                            ->formatStateUsing(fn ($state) => DateHelper::formatDate($state))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('roles.name')
                            ->label(__('filament.roles'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
