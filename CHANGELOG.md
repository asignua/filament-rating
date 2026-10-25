# Changelog

All notable changes to `asignua/filament-rating` are documented here.

## Unreleased

- Form field `Rating`, table column `RatingColumn` and infolist entry `RatingEntry` for Filament 5, API-compatible with
  `mokhosh/filament-rating` (`stars()`, `allowZero()`, `color()`, `size()`, `theme(RatingTheme::...)`).
- Half stars (`halfStars()`), `clearable()`, custom `icon()` / `emptyIcon()`, per-value `tooltips()`, `showValue()`.
- Field is a keyboard-operable `role="slider"` (arrow keys, Home / End, Delete), with `aria-valuetext` such as
  "3 of 5 stars"; columns and entries are `role="img"` with the same label. RTL mirrors, dark mode follows the panel.
- Validation: numeric, within the range, on the step.
- Namespaced stylesheet (`.fi-rating*`), no JavaScript build.
- Translations: en, uk, pl, de, fr, es, it, nl, pt_BR, tr.

- Review fixes: read-only values are never saved server side, blank/array payloads are handled, the real `aria-valuemin`, keyboard snaps to the step, `showValue()` prints the stored number, `extraAttributes()` on columns and entries, image icons are sized.
