<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\Reviews\Pages;

use Filament\Resources\Pages\ViewRecord;
use Workbench\App\Filament\Resources\Reviews\ReviewResource;

class ViewReview extends ViewRecord
{
    protected static string $resource = ReviewResource::class;
}
