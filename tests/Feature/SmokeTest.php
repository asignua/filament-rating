<?php

declare(strict_types=1);

namespace Asignua\FilamentRating\Tests\Feature;

use Asignua\FilamentRating\RatingPlugin;
use Asignua\FilamentRating\Tests\TestCase;
use Filament\Facades\Filament;

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
        $this->assertSame('Sample', __('filament-rating::filament-rating.sample'));
    }
}
