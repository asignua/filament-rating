# Filament Rating

[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/badges/StandWithUkraine.svg)](https://stand-with-ukraine.pp.ua)

<img class="filament-hidden" src="https://raw.githubusercontent.com/asignua/filament-rating/main/art/cover.jpg" alt="Filament Rating">

[![Tests](https://img.shields.io/github/actions/workflow/status/asignua/filament-rating/tests.yml?branch=main&label=tests)](https://github.com/asignua/filament-rating/actions/workflows/tests.yml)

Star ratings for Filament 5: a **form field**, a **table column** and an **infolist entry** that look the same everywhere.
Half stars, a "no stars" choice, custom icons (hearts, thumbs, anything from Blade Icons), per-value tooltips, an optional
"4.5" next to the stars. The field is a real slider for the keyboard and for screen readers ("3 of 5 stars"), mirrors in
RTL and follows the panel colours and dark mode. It is the successor of `mokhosh/filament-rating`, which stopped before
Filament 5, with the same class and method names so you migrate by swapping a namespace.

No JavaScript to build and no Tailwind safelist: the interaction is a few lines of inline Alpine, the stylesheet is
namespaced (`.fi-rating*`) and uses Filament's own colour variables. **Free (MIT), there is no Pro edition.**

## Screenshots

A form with whole stars, half stars and a 10-heart score:

![Rating fields in a form](https://raw.githubusercontent.com/asignua/filament-rating/main/art/form.jpg)

The same ratings in a table (compact) and in an infolist:

![Rating column in a table](https://raw.githubusercontent.com/asignua/filament-rating/main/art/table.jpg)

![Rating entries in an infolist](https://raw.githubusercontent.com/asignua/filament-rating/main/art/infolist.jpg)

Dark mode follows the panel:

![The form in dark mode](https://raw.githubusercontent.com/asignua/filament-rating/main/art/form-dark.jpg)

## Requirements

- PHP 8.3+
- Laravel 12 or 13
- Filament 5

## Installation

```bash
composer require asignua/filament-rating
php artisan filament:assets
```

`php artisan filament-rating:install` does the second step too. The plugin class is optional (the field, column and entry
work without it); register it if you prefer to list your plugins on the panel:

```php
use Asignua\FilamentRating\RatingPlugin;

$panel->plugin(RatingPlugin::make());
```

## Usage

```php
use Asignua\FilamentRating\Columns\RatingColumn;
use Asignua\FilamentRating\Components\Rating;
use Asignua\FilamentRating\Entries\RatingEntry;

// Form
Rating::make('rating')->required();

// Table
RatingColumn::make('rating')->sortable();

// Infolist
RatingEntry::make('rating');
```

The field stores a whole number (`4`). Store it in an integer column. With half stars it stores `3.5`, so use a `decimal(2,1)`
or `float` column; an empty field is `null`.

### Options

Every option is available on the field, the column and the entry (and takes a value or a closure), except where noted.

| Method | What it does | Default |
| --- | --- | --- |
| `stars(int $n)` | Number of stars | `5` |
| `halfStars()` / `theme(RatingTheme::HalfStars)` | Half steps (`3.5`) | whole stars |
| `allowZero()` | A "no stars" button that stores `0` | off |
| `clearable()` (field) | Clicking the current value (or pressing Delete) empties the field to `null` | off |
| `color('success')` | A Filament colour name that is registered on the panel (`primary`, `danger`, `success`, ... or your own via `FilamentColor`) or a palette (`Color::Amber`) | `primary` |
| `size('lg')` | `xs`, `sm`, `md`, `lg`, `xl` | `md` (`sm` in tables) |
| `icon('heroicon-s-heart')` | Filled star icon, any Blade icon | `heroicon-s-star` |
| `emptyIcon('heroicon-o-heart')` | Empty star icon | the filled icon, muted |
| `tooltips([...])` | Text per value, see below | none |
| `showValue()` | Print the number ("4.5") after the stars | off |
| `disabled()` / `readOnly()` (field) | Not operable, not focusable. A `readOnly()` value is also never saved: it is left out of the form state on the server, so a crafted request cannot change it (the stored value stays as it was). | off |

The field is a normal Filament field, so `required()`, `label()`, `live()`, `default()`, `helperText()`, `hint()` and the rest
work as usual. It validates that the value is numeric, between the minimum and the number of stars, and on the step (no `2.3` on half stars). The minimum is one step: `1` on whole stars, `0.5` on half stars, or `0` with `allowZero()`. Blank text counts as empty (`null`), anything that is not a number is rejected.

### Tooltips

```php
Rating::make('quality')
    ->tooltips([1 => 'Poor', 3 => 'Good', 5 => 'Excellent'])
    ->allowZero();

// half steps need string keys: PHP truncates a float array key
Rating::make('rating')->halfStars()->tooltips(['4.5' => 'Almost perfect']);

// or compute it
Rating::make('rating')->tooltips(fn (float $value): ?string => $value >= 4 ? 'Recommended' : null);
```

The text shows as a tooltip while hovering that value and is added to the spoken label ("3 of 5 stars - Good").

### Columns and entries

```php
RatingColumn::make('rating')->halfStars()->showValue()->size('xs');

RatingEntry::make('rating')->halfStars()->showValue()->size('lg')->color('warning');
```

Columns are compact (`sm`) by default. The stars are rounded to the step of the theme (4.3 shows as 4.5 stars on half stars,
4 on whole stars) and `showValue()` prints the stored number as it is (one decimal, not clamped to the number of stars). A record without a rating shows empty stars labelled
"No rating". Sorting and searching are Filament's own (`->sortable()`).

### Custom icons

```php
Rating::make('love')->icon('heroicon-s-heart')->emptyIcon('heroicon-o-heart')->color('danger')->stars(7);
```

## Accessibility

- The field is a single `role="slider"` tab stop with `aria-valuemin/max/now` (the real minimum; `aria-valuenow` is left out while empty) and a spoken value such as "3.5 of 5 stars".
  Arrow keys change the value by one step (they follow the reading direction in RTL), Home and End jump to the ends, Delete or
  Backspace clears (with `clearable()` or `allowZero()`). The focus ring is visible.
- Columns and entries are `role="img"` with the same label; the decorative icons are hidden from screen readers.
- Disabled and read-only fields are not focusable and carry `aria-disabled` / `aria-readonly`.
- RTL: the filled part and the hover preview mirror automatically.

## Migrating from `mokhosh/filament-rating`

```bash
composer remove mokhosh/filament-rating
composer require asignua/filament-rating
php artisan filament:assets
```

Then replace the namespace: `Mokhosh\FilamentRating\` becomes `Asignua\FilamentRating\` (`Components\Rating`,
`Columns\RatingColumn`, `Entries\RatingEntry`, `RatingTheme` keep their names and places).

| mokhosh | Here |
| --- | --- |
| `Rating::make()`, `RatingColumn::make()`, `RatingEntry::make()` | Same |
| `stars(int)`, `allowZero()` | Same |
| `color('primary' ...)`, `size('xs' ... 'xl')` | `size()` is the same. `color()` takes the panel's registered colours: mokhosh's `color('amber')` worked through Tailwind's `text-amber-500` class, here an unregistered name falls back to `primary`, so register the colour or pass `Color::Amber` |
| `theme(RatingTheme::Simple / HalfStars)` | Same (plus the shorthand `halfStars()`) |
| `getStars()`, `getStarsArray()`, `shouldAllowZero()`, `getColor()`, `getSize()`, `getTheme()` | Same |
| `filament-rating:install` | Same (runs `filament:assets`) |
| Published views `filament-rating::simple.*`, `half-stars.*`, `wrappers.*` | **Differ**: the views are `field`, `column`, `entry`; re-do any published and edited copy |
| Tailwind classes in the views (`text-primary-500`, ...) | **Differ**: the plugin ships its own `.fi-rating*` CSS, so nothing needs compiling in your theme |
| Public property `$color` / `$size` | **Differ**: use the getters |
| Stars are radio inputs | **Differ**: a single slider (keyboard, `aria-valuetext`) |
| (not available) | New: `clearable()`, `icon()`, `emptyIcon()`, `tooltips()`, `showValue()`, `readOnly()`, validation of range and step, a compact column |

The stored values are the same (an int, or `3.5` on half stars), so no data migration is needed.

## Translations

The interface ships in English, Ukrainian, German, Spanish, French, Italian, Dutch, Polish, Brazilian Portuguese and Turkish
under the `filament-rating::filament-rating` namespace. A test keeps every language in step with the English keys. Override a
string by publishing the translations (`--tag=filament-rating-translations`) and editing the copy in
`lang/vendor/filament-rating`.

## AI agents

The package ships [Laravel Boost](https://laravel.com/docs/boost) guidelines (`resources/boost/guidelines/core.blade.php`) so a
coding agent wires it up correctly.

## Testing

```bash
composer install
vendor/bin/phpunit
vendor/bin/phpstan analyse --memory-limit=1G
vendor/bin/pint --test
```

Try it by hand with the workbench (a Reviews resource with a field, a column and an infolist):

```bash
vendor/bin/testbench workbench:build
vendor/bin/testbench filament:assets
vendor/bin/testbench db:seed --class="Workbench\\Database\\Seeders\\DemoSeeder"
vendor/bin/testbench serve --host=0.0.0.0
```

Open `/admin/reviews` and log in as `emma@example.com` / `password`.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).
