@php
    /** @var \Asignua\FilamentRating\Columns\RatingColumn | \Asignua\FilamentRating\Entries\RatingEntry $rating */
    $value = $rating->getRatingValue();

    if ($value !== null) {
        // The stars show the value rounded to the step of the theme (whole or half); the number shows it as stored.
        $step = $rating->getStep();
        $shown = round($value / $step) * $step;
    }
@endphp

<div
    {{
        $attributes
            ->class([
                'fi-rating',
                'fi-rating-static',
                'fi-rating-size-'.$rating->getSize(),
            ])
            ->style([$rating->getRatingColorStyle()])
    }}
    role="img"
    aria-label="{{ $rating->getRatingText($value) }}"
    @if ($value !== null && filled($tooltip = $rating->getTooltipFor($value))) title="{{ $tooltip }}" @endif
>
    <span class="fi-rating-stars" aria-hidden="true">
        @foreach ($rating->getStarsArray() as $star)
            <span class="fi-rating-star">
                <span class="fi-rating-empty">{{ $rating->renderRatingIcon($rating->getRatingEmptyIcon()) }}</span>
                @if ($value !== null && ($fill = max(0, min(1, $shown - ($star - 1)))) > 0)
                    <span class="fi-rating-fill" style="width: {{ $fill * 100 }}%">
                        {{ $rating->renderRatingIcon($rating->getRatingIcon()) }}
                    </span>
                @endif
            </span>
        @endforeach
    </span>

    @if ($value !== null && $rating->shouldShowValue())
        <span class="fi-rating-value" aria-hidden="true">{{ $rating->formatRatingValue($value) }}</span>
    @endif
</div>
