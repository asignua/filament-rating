<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $product
 * @property string|null $comment
 * @property int|null $quality
 * @property float|null $rating
 * @property int|null $score
 */
class Review extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['quality' => 'integer', 'rating' => 'float', 'score' => 'integer'];
    }
}
