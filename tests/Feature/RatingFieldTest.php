<?php

declare(strict_types=1);

namespace Asignua\FilamentRating\Tests\Feature;

use Asignua\FilamentRating\Components\Rating;
use Asignua\FilamentRating\Tests\Fixtures\RatingForm;
use Asignua\FilamentRating\Tests\TestCase;
use Livewire\Livewire;
use Workbench\App\Filament\Resources\Reviews\Pages\CreateReview;
use Workbench\App\Filament\Resources\Reviews\Pages\EditReview;
use Workbench\App\Models\Review;

class RatingFieldTest extends TestCase
{
    public function test_it_creates_a_record_with_whole_and_half_values(): void
    {
        Livewire::test(CreateReview::class)
            ->fillForm(['product' => 'Box', 'rating' => 4.5, 'quality' => 3, 'score' => 7])
            ->call('create')
            ->assertHasNoFormErrors();

        $review = Review::query()->firstOrFail();

        $this->assertSame(4.5, $review->rating);
        $this->assertSame(3, $review->quality);
        $this->assertSame(7, $review->score);
    }

    public function test_it_hydrates_stored_values_and_saves_a_cleared_one_as_null(): void
    {
        $review = Review::query()->create(['product' => 'Box', 'rating' => 3.5, 'quality' => 4, 'score' => 2]);

        Livewire::test(EditReview::class, ['record' => $review->getKey()])
            ->assertSchemaStateSet(['rating' => 3.5, 'quality' => 4, 'score' => 2])
            ->fillForm(['rating' => 5, 'quality' => null])
            ->call('save')
            ->assertHasNoFormErrors();

        $review->refresh();

        $this->assertSame(5.0, $review->rating);
        $this->assertNull($review->quality);
    }

    public function test_allow_zero_accepts_zero_only_where_enabled(): void
    {
        Livewire::test(CreateReview::class)
            ->fillForm(['product' => 'Box', 'rating' => 0.5, 'quality' => 0, 'score' => 0])
            ->call('create')
            ->assertHasFormErrors(['score'])
            ->assertHasNoFormErrors(['quality', 'rating']);
    }

    public function test_it_is_required_when_marked_so(): void
    {
        Livewire::test(CreateReview::class)
            ->fillForm(['product' => 'Box', 'rating' => null])
            ->call('create')
            ->assertHasFormErrors(['rating' => 'required']);
    }

    public function test_it_rejects_values_above_the_maximum_and_non_numbers(): void
    {
        Livewire::test(CreateReview::class)
            ->fillForm(['product' => 'Box', 'rating' => 5.5, 'quality' => 6, 'score' => 'many'])
            ->call('create')
            ->assertHasFormErrors(['rating', 'quality', 'score']);
    }

    public function test_it_rejects_a_value_off_the_step(): void
    {
        Livewire::test(CreateReview::class)
            ->fillForm(['product' => 'Box', 'rating' => 2.3, 'quality' => 2.5, 'score' => 3])
            ->call('create')
            ->assertHasFormErrors(['rating', 'quality'])
            ->assertHasNoFormErrors(['score']);
    }

    public function test_it_renders_slider_semantics_and_the_filled_fraction(): void
    {
        $review = Review::query()->create(['product' => 'Box', 'rating' => 3.5, 'quality' => 2, 'score' => 6]);

        Livewire::test(EditReview::class, ['record' => $review->getKey()])
            ->assertSeeHtml('role="slider"')
            ->assertSeeHtml('aria-label="Overall"')
            ->assertSeeHtml('aria-valuemax="5"')
            ->assertSeeHtml('aria-valuemax="10"')
            ->assertSeeHtml('aria-valuenow="3.5"')
            ->assertSeeHtml('aria-valuetext="3.5 of 5 stars"')
            ->assertSeeHtml('aria-valuetext="2 of 5 stars - Fair"')
            ->assertSeeHtml('width: 50%')
            ->assertSeeHtml('--color-500:var(--success-500)')
            ->assertSeeHtml('fi-rating-size-sm')
            ->assertSeeHtml('<svg');
    }

    public function test_disabled_and_read_only_fields_are_not_operable(): void
    {
        $html = Livewire::test(RatingForm::class)->html();

        $this->assertStringContainsString('aria-disabled="true"', $html);
        $this->assertStringContainsString('aria-readonly="true"', $html);
        // Only the plain and the half field take part in tab order.
        $this->assertSame(2, substr_count($html, 'tabindex="0"'));
        $this->assertSame(1, substr_count($html, 'fi-rating-interactive') - 1);
    }

    public function test_it_wires_keyboard_and_pointer_handlers_and_the_zero_button(): void
    {
        $html = Livewire::test(RatingForm::class)->html();

        $this->assertStringContainsString('x-on:keydown="key($event)"', $html);
        $this->assertStringContainsString('x-on:click="set(', $html);
        $this->assertStringContainsString('fi-rating-zero', $html);
        $this->assertStringContainsString('aria-valuetext="2.5 of 5 stars - Meh"', $html);
        $this->assertStringContainsString('class="fi-rating-value"', $html);
    }

    public function test_options_and_defaults(): void
    {
        $field = Rating::make('r');

        $this->assertSame(5, $field->getStars());
        $this->assertSame([1, 2, 3, 4, 5], $field->getStarsArray());
        $this->assertSame('md', $field->getSize());
        $this->assertSame('primary', $field->getColor());
        $this->assertFalse($field->shouldAllowZero());
        $this->assertFalse($field->hasHalfStars());
        $this->assertSame(1.0, $field->getStep());
        $this->assertSame('heroicon-s-star', $field->getRatingEmptyIcon());

        $field->stars(3)->halfStars()->size('huge')->allowZero()->tooltips(fn (float $value): ?string => $value >= 3 ? 'Top' : null);

        $this->assertSame(3, $field->getStars());
        $this->assertSame(0.5, $field->getStep());
        $this->assertSame('md', $field->getSize(), 'An unknown size falls back to the default.');
        $this->assertSame(['3' => 'Top'], $field->getTooltipsMap());
    }

    public function test_cast_state(): void
    {
        $field = Rating::make('r');

        $this->assertNull($field->castRatingState(''));
        $this->assertSame(4, $field->castRatingState('4'));
        $this->assertSame(4, $field->castRatingState(4.0));
        $this->assertSame(3.5, $field->castRatingState('3.5'));
        $this->assertSame('abc', $field->castRatingState('abc'));
    }
}
