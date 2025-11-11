<?php

namespace App\Filament\Admin\Widgets;

use App\Helper\DateHelper;
use Carbon\Carbon;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class RecordMetadataWidget extends StatsOverviewWidget
{
    public ?Model $record = null;

    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        return [
            Stat::make(__('filament.created_at'), '')
                ->icon(Heroicon::OutlinedPlus)
                ->description(DateHelper::formatDate($this->getDate('created_at'))),

            Stat::make(__('filament.updated_at'), '')
                ->icon(Heroicon::OutlinedPencilSquare)
                ->description(DateHelper::formatDate($this->getDate('updated_at'))),

            Stat::make(__('filament.deleted_at'), '')
                ->icon(Heroicon::OutlinedTrash)
                ->description(DateHelper::formatDate($this->getDate('deleted_at'))),
        ];
    }

    private function getDate(string $attribute): ?Carbon
    {
        $value = $this->record?->{$attribute};

        return $value instanceof Carbon ? $value : null;
    }
}
