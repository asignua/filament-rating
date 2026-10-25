<?php

declare(strict_types=1);

namespace Asignua\FilamentRating\Tests\Fixtures;

use Asignua\FilamentRating\Components\Rating;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

/**
 * A bare Livewire form with one field per state, to assert how the field renders.
 */
class RatingForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(['plain' => 3, 'locked' => 4, 'frozen' => 2, 'half' => 2.5]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Rating::make('plain')->label('Plain'),
                Rating::make('locked')->label('Locked')->disabled(),
                Rating::make('frozen')->label('Frozen')->readOnly(),
                Rating::make('half')->label('Half')->halfStars()->clearable()->allowZero()->tooltips(['2.5' => 'Meh'])->showValue(),
            ])
            ->statePath('data');
    }

    /**
     * @return array<string, mixed>
     */
    public function saved(): array
    {
        return $this->form->getState();
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}
