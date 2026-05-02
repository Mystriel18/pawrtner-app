<?php

namespace App\Filament\Client\Resources\MedicalRecords\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MedicalRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pet.name')
                    ->searchable(),
                TextColumn::make('veterinarian.name')
                    ->label('Veterinarian')
                    ->sortable(),
                TextColumn::make('appointment.id')
                    ->label('Appointment')
                    ->searchable(),
                TextColumn::make('visit_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('chief_complaint')
                    ->searchable(),
                TextColumn::make('weight_kg')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('temperature_c')
                    ->numeric(2)
                    ->sortable(),
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
