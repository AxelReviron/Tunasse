<div {{ $getExtraAttributeBag() }} class="fi-ta-text px-3 py-4">
    @php
        $state = $getState();
        // $record est automatiquement disponible dans la vue de la colonne
        $color = $getColor() ?? $record?->color ?? '#666666';
        $label = $state instanceof \UnitEnum ? $state->getLabel() : ($state ?? 'N/A');
    @endphp

    @if($label)
        <div class="fi-ta-text-item inline-flex">
            <span class="fi-badge fi-size-sm ring-1 ring-inset px-2 py-1 rounded-md text-xs font-medium"
                  style="background-color: {{ $color }}22; color: {{ $color }}; ring-color: {{ $color }}33;">
                {{ $label }}
            </span>
        </div>
    @endif
</div>
