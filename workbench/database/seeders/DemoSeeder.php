<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Workbench\App\Models\Review;
use Workbench\App\Models\User;

/**
 * Screenshot data (`vendor/bin/testbench db:seed --class='Workbench\Database\Seeders\DemoSeeder'` after
 * `workbench:build`). Log in as emma@example.com / password.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(['email' => 'emma@example.com'], ['name' => 'Emma Carter', 'password' => 'password']);

        // [product, overall (half stars), quality (0-5), score (0-10), comment]
        $reviews = [
            ['Corrugated box 40x30x20', 4.5, 5, 9, 'Sturdy, ships flat, exactly the size on the label.'],
            ['Euro pallet EPAL', 4.0, 4, 8, 'Good pallet, two had loose nails.'],
            ['Stretch film 500 mm', 3.5, 3, 7, 'Does the job; the core is a bit weak.'],
            ['Thermal label roll 100x150', 5.0, 5, 10, 'Prints sharp, no jams in two months.'],
            ['Packing tape, brown', 2.5, 2, 4, 'Peels at the edges in the cold.'],
            ['Bubble wrap 50 m', 1.5, 1, 3, 'Half of the bubbles were already flat.'],
            ['Paper filler 10 kg', 3.0, 3, 6, null],
            ['Wooden crate 60x40', 0.5, 0, 1, 'Arrived broken.'],
            ['Strapping band PP 12 mm', null, null, null, null],
        ];

        foreach ($reviews as [$product, $rating, $quality, $score, $comment]) {
            Review::query()->firstOrCreate(['product' => $product], [
                'rating' => $rating,
                'quality' => $quality,
                'score' => $score,
                'comment' => $comment,
            ]);
        }
    }
}
