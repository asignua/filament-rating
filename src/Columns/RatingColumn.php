<?php

declare(strict_types=1);

namespace Asignua\FilamentRating\Columns;

use Asignua\FilamentRating\Concerns\HasRatingOptions;
use Filament\Tables\Columns\Column;

/**
 * Read-only stars in a table cell. Compact by default (`sm`).
 */
class RatingColumn extends Column
{
    use HasRatingOptions;

    /** @var view-string */
    protected string $view = 'filament-rating::column';

    protected function getDefaultSize(): string
    {
        return 'sm';
    }
}
