<?php

namespace App\Filament\Tables\Columns;

use Filament\Tables\Columns\Column;
use Closure;

class ColoredBadgeColumn extends Column
{
    protected string $view = 'filament.tables.columns.colored-badge-column';

    protected float | Closure | null $customColor = null;

    public function color(string | Closure | null $color): static
    {
        $this->customColor = $color;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->evaluate($this->customColor);
    }
}
