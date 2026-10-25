<?php

declare(strict_types=1);

namespace Asignua\FilamentRating\Components;

use Asignua\FilamentRating\Concerns\HasRatingOptions;
use Closure;
use Filament\Forms\Components\Concerns\CanBeReadOnly;
use Filament\Forms\Components\Field;

/**
 * Star rating input. Stores an int (or a float with half stars), `null` when empty.
 */
class Rating extends Field
{
    use CanBeReadOnly;
    use HasRatingOptions;

    /** @var view-string */
    protected string $view = 'filament-rating::field';

    protected bool|Closure $isClearable = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (Rating $component, mixed $state): void {
            $component->state($component->castRatingState($state));
        });

        $this->dehydrateStateUsing(fn (Rating $component, mixed $state): int|float|string|null => $component->dehydrateRatingState($state));

        // A read-only field is not operable in the browser; server side its value is simply never saved.
        $this->dehydrated(fn (Rating $component): bool => !$component->isReadOnly());

        $this->rule(static fn (Rating $component): Closure => function (string $attribute, mixed $value, Closure $fail) use ($component): void {
            $label = $component->getValidationAttribute();

            if (!is_scalar($value) && $value !== null) {
                $fail(__('validation.numeric', ['attribute' => $label]));

                return;
            }

            if (blank($value)) {
                return;
            }

            if (!is_numeric($value)) {
                $fail(__('validation.numeric', ['attribute' => $label]));

                return;
            }

            $min = $component->getRatingMin();
            $max = $component->getStars();

            if ($value < $min) {
                $fail(__('validation.min.numeric', ['attribute' => $label, 'min' => $component->formatRatingValue($min)]));
            } elseif ($value > $max) {
                $fail(__('validation.max.numeric', ['attribute' => $label, 'max' => $max]));
            } elseif (fmod((float) $value / $component->getStep(), 1.0) !== 0.0) {
                $fail(__('filament-rating::filament-rating.invalid_step', ['attribute' => $label, 'step' => $component->formatRatingValue($component->getStep())]));
            }
        });
    }

    /**
     * Clicking the current value (or pressing Delete) empties the field (`null`).
     */
    public function clearable(bool|Closure $condition = true): static
    {
        $this->isClearable = $condition;

        return $this;
    }

    public function isClearable(): bool
    {
        return (bool) $this->evaluate($this->isClearable);
    }

    public function isInteractive(): bool
    {
        return !$this->isDisabled() && !$this->isReadOnly();
    }

    /**
     * "4" / 4.0 become 4, "3.5" becomes 3.5, empty becomes null; anything non-numeric is left for the validator.
     */
    public function castRatingState(mixed $state): mixed
    {
        if (is_string($state)) {
            $state = trim($state);
        }

        if ($state === null || $state === '') {
            return null;
        }

        if (!is_numeric($state)) {
            return $state;
        }

        $number = (float) $state;

        return floor($number) === $number ? (int) $number : $number;
    }

    /**
     * What is saved: a number or null; anything else (an array from a crafted request) never reaches the model.
     */
    public function dehydrateRatingState(mixed $state): int|float|string|null
    {
        $state = $this->castRatingState($state);

        return is_int($state) || is_float($state) ? $state : (is_string($state) && is_numeric($state) ? $state : null);
    }
}
