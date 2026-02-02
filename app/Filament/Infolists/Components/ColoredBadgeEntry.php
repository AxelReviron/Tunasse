<?php

namespace App\Filament\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Entry;

class ColoredBadgeEntry extends Entry
{
    protected string $view = 'filament.infolists.components.colored-badge-entry';

    protected Closure|string|null $customColor = null;

    // Permet de passer une couleur manuellement : ->color('#ff0000')
    public function color(Closure|string|null $color): static
    {
        $this->customColor = $color;

        return $this;
    }

    public function getColor(): ?string
    {
        // On évalue la closure si c'en est une, sinon on prend la valeur
        return $this->evaluate($this->customColor);
    }
}
