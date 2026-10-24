<?php

declare(strict_types=1);

namespace Asignua\FilamentRating\Tests\Feature;

use Asignua\FilamentRating\Tests\TestCase;
use Livewire\Livewire;
use Workbench\App\Filament\Resources\Reviews\Pages\ListReviews;
use Workbench\App\Filament\Resources\Reviews\Pages\ViewReview;
use Workbench\App\Models\Review;

class RatingDisplayTest extends TestCase
{
    public function test_the_column_renders_a_compact_labelled_image_with_the_number(): void
    {
        $review = Review::query()->create(['product' => 'Box', 'rating' => 4.5, 'quality' => 3, 'score' => 8]);

        Livewire::test(ListReviews::class)
            ->assertCanSeeTableRecords([$review])
            ->assertSeeHtml('fi-ta-rating')
            ->assertSeeHtml('role="img"')
            ->assertSeeHtml('aria-label="4.5 of 5 stars"')
            ->assertSeeHtml('aria-label="3 of 5 stars - Good"')
            ->assertSeeHtml('aria-label="8 of 10 stars"')
            ->assertSeeHtml('fi-rating-size-sm')
            ->assertSeeHtml('fi-rating-size-xs')
            ->assertSeeHtml('<span class="fi-rating-value" aria-hidden="true">4.5</span>')
            ->assertSeeHtml('width: 50%')
            ->assertSeeHtml('width: 100%');
    }

    public function test_the_column_says_so_when_there_is_no_rating(): void
    {
        Review::query()->create(['product' => 'Box']);

        Livewire::test(ListReviews::class)
            ->assertSeeHtml('aria-label="'.__('filament-rating::filament-rating.no_rating').'"')
            ->assertDontSeeHtml('class="fi-rating-fill"')
            ->assertDontSeeHtml('class="fi-rating-value"');
    }

    public function test_the_column_sorts(): void
    {
        $low = Review::query()->create(['product' => 'Low', 'rating' => 1]);
        $high = Review::query()->create(['product' => 'High', 'rating' => 5]);

        Livewire::test(ListReviews::class)
            ->sortTable('rating', 'desc')
            ->assertCanSeeTableRecords([$high, $low], inOrder: true);
    }

    public function test_the_entry_renders_the_same_stars(): void
    {
        $review = Review::query()->create(['product' => 'Box', 'rating' => 2.5, 'quality' => 5, 'score' => 10]);

        Livewire::test(ViewReview::class, ['record' => $review->getKey()])
            ->assertSeeHtml('aria-label="2.5 of 5 stars"')
            ->assertSeeHtml('aria-label="5 of 5 stars - Excellent"')
            ->assertSeeHtml('fi-rating-size-lg')
            ->assertSeeHtml('--color-500:var(--warning-500)')
            ->assertSeeHtml('class="fi-rating-value"');
    }

    public function test_stars_are_rounded_to_the_step_but_the_number_is_not(): void
    {
        Review::query()->create(['product' => 'Box', 'rating' => 4.3]);

        // 4.3 -> 4.5 on half stars: the last star is half filled; the number stays 4.3.
        Livewire::test(ListReviews::class)
            ->assertSeeHtml('width: 50%')
            ->assertSeeHtml('>4.3</span>');
    }
}
