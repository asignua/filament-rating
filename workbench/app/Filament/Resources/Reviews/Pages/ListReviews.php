<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\Reviews\Pages;

use Filament\Resources\Pages\ListRecords;
use Workbench\App\Filament\Resources\Reviews\ReviewResource;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;
}
