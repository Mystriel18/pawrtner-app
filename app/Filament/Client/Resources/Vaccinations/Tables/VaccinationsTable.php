<?php

namespace App\Filament\Client\Resources\Vaccinations\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VaccinationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pet.name')
                    ->searchable(),
                TextColumn::make('medicalRecord.id')
                    ->searchable(),
                TextColumn::make('administeredBy.name')
                    ->label('Administered By')
                    ->sortable(),
                TextColumn::make('vaccine_name')
                    ->searchable(),
                TextColumn::make('manufacturer')
                    ->searchable(),
                TextColumn::make('batch_number')
                    ->searchable(),
                TextColumn::make('administered_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('next_due_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
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
            ->recordActions([])
            ->toolbarActions([]);
    }
}
