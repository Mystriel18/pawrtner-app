<?php

namespace App\Filament\Client\Widgets;

use App\Filament\Client\Resources\Pets\PetResource;
use App\Models\Pet;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class MyPetsWidget extends TableWidget
{
    protected static ?string $heading = 'My Pets';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Pet::query()
                ->where('owner_user_id', auth()->id())
                ->orderBy('name'))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('species')
                    ->badge()
                    ->searchable(),
                TextColumn::make('breed')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('sex')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('weight_kg')
                    ->numeric(2)
                    ->suffix(' kg')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->headerActions([
                Action::make('addPet')
                    ->label('Add Pet')
                    ->icon('heroicon-o-plus')
                    ->url(PetResource::getUrl('create')),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (Pet $record): string => PetResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([]);
    }
}
