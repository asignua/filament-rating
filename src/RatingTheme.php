<?php

declare(strict_types=1);

namespace Asignua\FilamentRating;

/**
 * Same cases as `Mokhosh\FilamentRating\RatingTheme`, so `->theme(RatingTheme::HalfStars)` keeps working.
 */
enum RatingTheme
{
    case Simple;
    case HalfStars;

    /**
     * The smallest selectable step: a whole star or half of one.
     */
    public function step(): float
    {
        return match ($this) {
            self::Simple => 1.0,
            self::HalfStars => 0.5,
        };
    }
}
