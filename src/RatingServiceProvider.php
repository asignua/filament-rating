<?php

declare(strict_types=1);

namespace Asignua\FilamentRating;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RatingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-rating';

    public function configurePackage(Package $package): void
    {
        // Translations live in resources/lang/<locale>/filament-rating.php and are read as
        // `__('filament-rating::filament-rating.<key>')`. Publish tag: `filament-rating-translations`.
        $package->name(static::$name)
            ->hasTranslations()
            ->hasViews();

        // Add a config file only when the plugin really has options: create config/filament-rating.php and
        // chain `->hasConfigFile()` here (publish tag `filament-rating-config`). Prefer fluent setters on the Plugin.
    }
}
