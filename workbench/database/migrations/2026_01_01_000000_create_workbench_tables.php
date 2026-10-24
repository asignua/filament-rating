<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // `workbench:build` runs Testbench's own users migration first; the workbench owns this table.
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->string('product');
            $table->text('comment')->nullable();
            // Whole stars with a "0" choice, half stars (decimal), and a 10-point score.
            $table->unsignedTinyInteger('quality')->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->timestamps();
        });
    }
};
