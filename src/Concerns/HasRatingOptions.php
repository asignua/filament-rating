<?php

declare(strict_types=1);

namespace Asignua\FilamentRating\Concerns;

use Asignua\FilamentRating\RatingTheme;
use Closure;

use function Filament\Support\generate_icon_html;
use function Filament\Support\get_color_css_variables;

use Illuminate\Contracts\Support\Htmlable;

/**
 * The options shared by the form field, the table column and the infolist entry.
 *
 * Method names follow `mokhosh/filament-rating` (`stars`, `allowZero`, `color`, `size`, `theme`), plus the extras of
 * this package. Every setter takes a plain value or a closure.
 */
trait HasRatingOptions
{
    protected int|Closure $stars = 5;

    protected bool|Closure $allowZero = false;

    /** @var array<int, string>|Closure|string */
    protected string|array|Closure $color = 'primary';

    protected string|Closure|null $size = null;

    protected RatingTheme|Closure $theme = RatingTheme::Simple;

    protected string|Closure $ratingIcon = 'heroicon-s-star';

    protected string|Closure|null $ratingEmptyIcon = null;

    /** @var array<int|string, string>|Closure */
    protected array|Closure $tooltips = [];

    protected bool|Closure $showValue = false;

    public function stars(int|Closure $stars): static
    {
        $this->stars = $stars;

        return $this;
    }

    public function getStars(): int
    {
        return max(1, (int) $this->evaluate($this->stars));
    }

    /**
     * @return array<int, int>
     */
    public function getStarsArray(): array
    {
        return range(1, $this->getStars());
    }

    /**
     * Offer a "no stars" choice that is stored as `0` (as opposed to `clearable()`, which stores `null`).
     */
    public function allowZero(bool|Closure $allowZero = true): static
    {
        $this->allowZero = $allowZero;

        return $this;
    }

    public function shouldAllowZero(): bool
    {
        return (bool) $this->evaluate($this->allowZero);
    }

    /**
     * A Filament colour name (`primary`, `danger`, a colour registered with `FilamentColor`) or a full shade palette.
     *
     * @param array<int, string>|Closure|string $color
     */
    public function color(string|array|Closure $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return array<int, string>|string
     */
    public function getColor(): string|array
    {
        return $this->evaluate($this->color) ?: 'primary';
    }

    /**
     * `xs`, `sm`, `md`, `lg` or `xl`.
     */
    public function size(string|Closure|null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): string
    {
        $size = $this->evaluate($this->size);

        return in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'], true) ? $size : $this->getDefaultSize();
    }

    protected function getDefaultSize(): string
    {
        return 'md';
    }

    public function theme(RatingTheme|Closure $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getTheme(): RatingTheme
    {
        return $this->evaluate($this->theme);
    }

    /**
     * Shorthand for `theme(RatingTheme::HalfStars)`: values like 3.5 can be picked and are stored as floats.
     */
    public function halfStars(bool|Closure $condition = true): static
    {
        $this->theme = fn (): RatingTheme => $this->evaluate($condition) ? RatingTheme::HalfStars : RatingTheme::Simple;

        return $this;
    }

    public function hasHalfStars(): bool
    {
        return $this->getTheme() === RatingTheme::HalfStars;
    }

    public function getStep(): float
    {
        return $this->getTheme()->step();
    }

    /**
     * The icon of a filled star (any Blade icon name, Heroicons by default).
     */
    public function icon(string|Closure $icon): static
    {
        $this->ratingIcon = $icon;

        return $this;
    }

    /**
     * The icon of an empty star; defaults to the filled one in a muted colour.
     */
    public function emptyIcon(string|Closure|null $icon): static
    {
        $this->ratingEmptyIcon = $icon;

        return $this;
    }

    public function getRatingIcon(): string
    {
        return $this->evaluate($this->ratingIcon);
    }

    public function getRatingEmptyIcon(): string
    {
        return $this->evaluate($this->ratingEmptyIcon) ?? $this->getRatingIcon();
    }

    public function renderRatingIcon(string $icon): ?Htmlable
    {
        return generate_icon_html($icon);
    }

    /**
     * Labels per value, e.g. `[1 => 'Poor', 3 => 'Good', 5 => 'Excellent']`. Half values need string keys
     * (`'3.5' => '…'`, a float array key is truncated by PHP). A closure receives `$value` (float) and returns a string.
     *
     * @param array<int|string, string>|Closure $tooltips
     */
    public function tooltips(array|Closure $tooltips): static
    {
        $this->tooltips = $tooltips;

        return $this;
    }

    public function getTooltipFor(float $value): ?string
    {
        $tooltips = $this->tooltips;

        if ($tooltips instanceof Closure) {
            $label = $this->evaluate($tooltips, ['value' => $value, 'rating' => $value]);

            return filled($label) ? (string) $label : null;
        }

        $label = $tooltips[$this->formatRatingValue($value)] ?? null;

        return filled($label) ? (string) $label : null;
    }

    /**
     * @return array<string, string>
     */
    public function getTooltipsMap(): array
    {
        $map = [];

        for ($value = ($this->shouldAllowZero() ? 0.0 : $this->getStep()); $value <= $this->getStars(); $value += $this->getStep()) {
            if (filled($label = $this->getTooltipFor($value))) {
                $map[$this->formatRatingValue($value)] = $label;
            }
        }

        return $map;
    }

    /**
     * Print the number next to the stars ("4.5").
     */
    public function showValue(bool|Closure $condition = true): static
    {
        $this->showValue = $condition;

        return $this;
    }

    public function shouldShowValue(): bool
    {
        return (bool) $this->evaluate($this->showValue);
    }

    public function formatRatingValue(float $value): string
    {
        return rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');
    }

    /**
     * "3.5 of 5 stars" (+ the label of that value when there is one).
     */
    public function getRatingText(?float $value): string
    {
        if ($value === null) {
            return __('filament-rating::filament-rating.no_rating');
        }

        $text = __('filament-rating::filament-rating.stars_of', [
            'value' => $this->formatRatingValue($value),
            'max' => $this->getStars(),
        ]);

        return filled($label = $this->getTooltipFor($value)) ? "{$text} - {$label}" : $text;
    }

    /**
     * The CSS variables (`--color-400/500/600`) the stylesheet paints the stars with.
     */
    public function getRatingColorStyle(): string
    {
        // A custom palette is the usual Filament shade map (`Color::Rose`); the helper types it as an exact shape.
        /** @phpstan-ignore argument.type */
        return get_color_css_variables($this->getColor(), shades: [400, 500, 600]) ?? '';
    }

    public function getRatingMin(): float
    {
        return $this->shouldAllowZero() ? 0.0 : $this->getStep();
    }

    /**
     * The stored number as it is (not clamped), for `showValue()`.
     */
    public function getRatingNumber(): ?float
    {
        $state = $this->getState();

        return is_numeric($state) ? (float) $state : null;
    }

    /**
     * The stored value as a number clamped to 0..stars, or null when there is none.
     */
    public function getRatingValue(): ?float
    {
        $state = $this->getState();

        if (!is_numeric($state)) {
            return null;
        }

        return min(max((float) $state, 0.0), (float) $this->getStars());
    }
}
