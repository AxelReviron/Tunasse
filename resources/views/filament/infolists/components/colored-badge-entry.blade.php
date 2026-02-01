<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="fi-in-text">
        <div class="fi-in-text-item fi-in-text-has-badges">
            @php
                $state = $getState();
                $record = $getRecord();
                $color = $getColor() ?? $record?->color ?? '#666666';
                $label = $state instanceof \UnitEnum ? $state->getLabel() : ($state ?? 'N/A');
            @endphp

            @if($label)
                <span class="fi-badge fi-size-sm ring-1 ring-inset px-2 py-1 rounded-md text-xs font-medium inline-flex"
                      style="background-color: {{ $color }}22; color: {{ $color }}; ring-color: {{ $color }}33;">
                    {{ $label }}
                </span>
            @endif
        </div>
    </div>
</x-dynamic-component>
