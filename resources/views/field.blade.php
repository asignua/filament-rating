@php
    use Illuminate\Support\Js;

    $statePath = $getStatePath();
    $interactive = $isInteractive();
    $max = $getStars();
    $step = $getStep();
    $allowZero = $shouldAllowZero();
    $current = $getRatingValue();
    $label = trim(strip_tags((string) $getLabel())) ?: __('filament-rating::filament-rating.rating');
    $config = [
        'max' => $max,
        'step' => $step,
        'min' => $allowZero ? 0 : $step,
        'allowZero' => $allowZero,
        'clearable' => $isClearable(),
        'interactive' => $interactive,
        'labels' => $getTooltipsMap(),
        'template' => __('filament-rating::filament-rating.stars_of'),
        'none' => __('filament-rating::filament-rating.no_rating'),
    ];
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
            hover: null,
            ...{{ Js::from($config) }},
            num(value) {
                value = parseFloat(value)

                return Number.isNaN(value) ? 0 : value
            },
            get shown() {
                return this.hover ?? this.num(this.state)
            },
            fill(star) {
                return Math.max(0, Math.min(1, this.shown - (star - 1))) * 100
            },
            isRtl() {
                return getComputedStyle(this.$el).direction === 'rtl'
            },
            at(star, event) {
                if (this.step >= 1) {
                    return star
                }

                const box = event.currentTarget.getBoundingClientRect()
                let fraction = (event.clientX - box.left) / box.width

                if (this.isRtl()) {
                    fraction = 1 - fraction
                }

                return fraction <= 0.5 ? star - 0.5 : star
            },
            set(value) {
                if (! this.interactive) {
                    return
                }

                this.state =
                    this.clearable && this.state !== null && this.state !== '' && this.num(this.state) === value
                        ? null
                        : value
            },
            key(event) {
                if (! this.interactive) {
                    return
                }

                const rtl = this.isRtl()
                const current = this.state === null || this.state === '' ? null : this.num(this.state)
                let next

                if (event.key === 'ArrowUp' || event.key === (rtl ? 'ArrowLeft' : 'ArrowRight')) {
                    next = current === null ? this.min : Math.min(this.max, current + this.step)
                } else if (event.key === 'ArrowDown' || event.key === (rtl ? 'ArrowRight' : 'ArrowLeft')) {
                    next = current === null ? null : current - this.step < this.min ? (this.clearable ? null : this.min) : current - this.step
                } else if (event.key === 'Home') {
                    next = this.min
                } else if (event.key === 'End') {
                    next = this.max
                } else if (event.key === 'Delete' || event.key === 'Backspace') {
                    if (! this.clearable && ! this.allowZero) {
                        return
                    }

                    next = this.clearable ? null : 0
                } else {
                    return
                }

                event.preventDefault()
                this.state = next
            },
            fmt(value) {
                return String(Math.round(value * 10) / 10)
            },
            text() {
                if (this.state === null || this.state === '') {
                    return this.none
                }

                const value = this.fmt(this.num(this.state))
                const text = this.template.replace(':value', value).replace(':max', this.max)

                return this.labels[value] ? text + ' - ' + this.labels[value] : text
            },
        }"
        x-on:mouseleave="hover = null"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-rating',
                    'fi-rating-size-'.$getSize(),
                    'fi-rating-interactive' => $interactive,
                ])
                ->style([$getRatingColorStyle()])
        }}
    >
        @if ($allowZero)
            <button
                type="button"
                tabindex="-1"
                aria-hidden="true"
                class="fi-rating-zero"
                title="{{ __('filament-rating::filament-rating.no_stars') }}"
                x-bind:class="{ 'fi-active': state !== null && state !== '' && num(state) === 0 }"
                x-on:click="set(0)"
                @disabled(! $interactive)
            >
                {{ \Filament\Support\generate_icon_html('heroicon-c-no-symbol') }}
            </button>
        @endif

        <div
            id="{{ $getId() }}"
            class="fi-rating-stars"
            role="slider"
            tabindex="{{ $interactive ? 0 : -1 }}"
            aria-label="{{ $label }}"
            aria-valuemin="0"
            aria-valuemax="{{ $max }}"
            aria-valuenow="{{ $current === null ? 0 : $formatRatingValue($current) }}"
            aria-valuetext="{{ $getRatingText($current) }}"
            @if (! $interactive) aria-disabled="true" @endif
            @if ($isReadOnly()) aria-readonly="true" @endif
            @if ($isRequired()) aria-required="true" @endif
            x-bind:aria-valuenow="state === null || state === '' ? 0 : fmt(num(state))"
            x-bind:aria-valuetext="text()"
            x-bind:title="hover === null ? null : labels[fmt(hover)] ?? null"
            x-on:keydown="key($event)"
        >
            @foreach ($getStarsArray() as $star)
                <span
                    class="fi-rating-star"
                    data-star="{{ $star }}"
                    x-on:mousemove="interactive && (hover = at({{ $star }}, $event))"
                    x-on:click="set(at({{ $star }}, $event))"
                >
                    <span class="fi-rating-empty">{{ $renderRatingIcon($getRatingEmptyIcon()) }}</span>
                    <span
                        class="fi-rating-fill"
                        style="width: {{ $current === null ? 0 : max(0, min(1, $current - ($star - 1))) * 100 }}%"
                        x-bind:style="{ width: fill({{ $star }}) + '%' }"
                    >
                        {{ $renderRatingIcon($getRatingIcon()) }}
                    </span>
                </span>
            @endforeach
        </div>

        @if ($shouldShowValue())
            <span class="fi-rating-value" x-text="state === null || state === '' ? '' : fmt(num(state))">
                {{ $current === null ? '' : $formatRatingValue($current) }}
            </span>
        @endif
    </div>
</x-dynamic-component>
