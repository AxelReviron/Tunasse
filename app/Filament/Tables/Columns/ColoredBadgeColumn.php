<?php

namespace App\Filament\Tables\Columns;

use Closure;
use Filament\Tables\Columns\Column;

class ColoredBadgeColumn extends Column
{
    protected string $view = 'filament.tables.columns.colored-badge-column';

    protected float|Closure|null $customColor = null;

    public function color(string|Closure|null $color): static
    {
        $this->customColor = $color;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->evaluate($this->customColor);
    }
}
