<?php

declare(strict_types=1);

namespace Asignua\FilamentRating\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'filament-rating:install';

    protected $description = 'Publish the Filament assets (the plugin stylesheet) - same as `php artisan filament:assets`.';

    public function handle(): int
    {
        return $this->call('filament:assets');
    }
}
