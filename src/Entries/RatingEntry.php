<?php

declare(strict_types=1);

namespace Asignua\FilamentRating\Entries;

use Asignua\FilamentRating\Concerns\HasRatingOptions;
use Filament\Infolists\Components\Entry;

/**
 * Read-only stars in an infolist.
 */
class RatingEntry extends Entry
{
    use HasRatingOptions;

    /** @var view-string */
    protected string $view = 'filament-rating::entry';
}
