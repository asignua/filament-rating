## Filament Rating (asignua/filament-rating)

- Successor of `mokhosh/filament-rating`; migrate by swapping the namespace to `Asignua\FilamentRating\`.
- Install: `composer require asignua/filament-rating` then `php artisan filament:assets` (or `php artisan filament-rating:install`). The panel plugin is optional.
- Form field `Asignua\FilamentRating\Components\Rating::make('rating')`, table column `Columns\RatingColumn`, infolist entry `Entries\RatingEntry`. They share the options `stars(int)`, `allowZero()`, `color()`, `size('xs'|'sm'|'md'|'lg'|'xl')`, `theme(RatingTheme::HalfStars)` / `halfStars()`, `icon()`, `emptyIcon()`, `tooltips([...])`, `showValue()`; the field adds `clearable()`.
- The field stores an int, or a float with half stars (use a `decimal(2,1)` / `float` column), and `null` when empty. `allowZero()` stores `0`, `clearable()` stores `null`.
- Do not add Tailwind classes for the stars: the plugin ships its own `.fi-rating*` stylesheet, registered through `FilamentAsset`.
- Half-step tooltip keys must be strings (`'3.5' => 'Good'`): a float array key is truncated by PHP.
