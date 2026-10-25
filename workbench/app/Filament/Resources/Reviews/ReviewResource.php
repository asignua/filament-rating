<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\Reviews;

use Asignua\FilamentRating\Columns\RatingColumn;
use Asignua\FilamentRating\Components\Rating;
use Asignua\FilamentRating\Entries\RatingEntry;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Workbench\App\Filament\Resources\Reviews\Pages\CreateReview;
use Workbench\App\Filament\Resources\Reviews\Pages\EditReview;
use Workbench\App\Filament\Resources\Reviews\Pages\ListReviews;
use Workbench\App\Filament\Resources\Reviews\Pages\ViewReview;
use Workbench\App\Models\Review;

/**
 * Product reviews: the stars in a form, a table and an infolist (README screenshots).
 */
class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-star';

    /** @var array<int, string> */
    public static array $qualityLabels = [0 => 'Unusable', 1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Very good', 5 => 'Excellent'];

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('product')->required(),
            Rating::make('rating')
                ->label('Overall')
                ->halfStars()
                ->showValue()
                ->clearable()
                ->required(),
            Rating::make('quality')
                ->allowZero()
                ->color('success')
                ->tooltips(static::$qualityLabels),
            Rating::make('score')
                ->label('Score (out of 10)')
                ->stars(10)
                ->size('sm')
                ->color('warning')
                ->icon('heroicon-s-heart')
                ->emptyIcon('heroicon-o-heart'),
            Textarea::make('comment')->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            RatingEntry::make('rating')->label('Overall')->halfStars()->showValue()->size('lg')->extraAttributes(['data-probe' => 'ent']),
            RatingEntry::make('quality')->color('success')->tooltips(static::$qualityLabels),
            RatingEntry::make('score')->stars(10)->size('sm')->color('warning')->icon('heroicon-s-heart')->emptyIcon('heroicon-o-heart'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product')->searchable()->weight('medium'),
                RatingColumn::make('rating')->label('Overall')->halfStars()->showValue()->extraAttributes(['data-probe' => 'col'])->sortable(),
                RatingColumn::make('quality')->color('success')->tooltips(static::$qualityLabels)->sortable(),
                RatingColumn::make('score')->stars(10)->size('xs')->color('warning')->icon('heroicon-s-heart')->emptyIcon('heroicon-o-heart'),
                TextColumn::make('comment')->limit(40)->color('gray'),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->defaultSort('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'view' => ViewReview::route('/{record}'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
