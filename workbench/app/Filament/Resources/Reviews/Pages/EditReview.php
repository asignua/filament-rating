<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\Reviews\Pages;

use Filament\Resources\Pages\EditRecord;
use Workbench\App\Filament\Resources\Reviews\ReviewResource;

class EditReview extends EditRecord
{
    protected static string $resource = ReviewResource::class;
}
