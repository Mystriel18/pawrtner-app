<?php

namespace App\Filament\Client\Resources\Pets\Tables;

use App\Models\Pet;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_photo_path')
                    ->label('Photo')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn () => asset('images/pet-placeholder.png')),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('species')
                    ->searchable(),
                TextColumn::make('breed')
                    ->searchable(),
                TextColumn::make('sex')
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('color')
                    ->searchable(),
                TextColumn::make('microchip_number')
                    ->searchable(),
                TextColumn::make('weight_kg')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Pet $record): bool => $record->owner_user_id === auth()->id()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
