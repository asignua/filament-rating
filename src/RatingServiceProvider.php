<?php

declare(strict_types=1);

namespace Asignua\FilamentRating;

use Asignua\FilamentRating\Commands\InstallCommand;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
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
            ->hasViews()
            ->hasCommand(InstallCommand::class);
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('filament-rating', __DIR__.'/../resources/dist/filament-rating.css'),
        ], 'asignua/filament-rating');
    }
}
