<?php

namespace App\Filament\Vet\Resources\MedicalRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                TextColumn::make('veterinarian_user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('appointment.id')
                    ->searchable(),
                TextColumn::make('visit_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('chief_complaint')
                    ->searchable(),
                TextColumn::make('weight_kg')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('temperature_c')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_by')
                    ->numeric()
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
