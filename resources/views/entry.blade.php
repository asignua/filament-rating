<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @include('filament-rating::partials.static', ['rating' => $entry])
</x-dynamic-component>
