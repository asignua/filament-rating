<?php

declare(strict_types=1);

namespace Asignua\FilamentRating\Tests\Feature;

use Asignua\FilamentRating\RatingPlugin;
use Asignua\FilamentRating\Tests\TestCase;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentAsset;

class SmokeTest extends TestCase
{
    public function test_the_panel_boots(): void
    {
        $this->assertSame('admin', Filament::getCurrentPanel()?->getId());
    }

    public function test_the_plugin_is_registered_on_the_panel(): void
    {
        $panel = Filament::getPanel('admin');

        $this->assertTrue($panel->hasPlugin('asignua-filament-rating'));
        $this->assertInstanceOf(RatingPlugin::class, $panel->getPlugin('asignua-filament-rating'));
    }

    public function test_the_translations_are_loaded(): void
    {
        $this->assertSame('No rating', __('filament-rating::filament-rating.no_rating'));
    }

    public function test_the_stylesheet_is_registered_and_does_not_open_a_layer(): void
    {
        $this->assertNotEmpty(FilamentAsset::getStyleHref('filament-rating', 'asignua/filament-rating'));

        $css = (string) file_get_contents(__DIR__.'/../../resources/dist/filament-rating.css');

        $this->assertStringContainsString('.fi-rating', $css);
        $this->assertStringNotContainsString('@layer', $css);
    }

    public function test_the_install_command_publishes_the_assets(): void
    {
        $this->artisan('filament-rating:install')->assertSuccessful();
    }
}
