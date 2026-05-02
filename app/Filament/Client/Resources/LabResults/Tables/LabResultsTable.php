<?php

namespace App\Filament\Client\Resources\LabResults\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LabResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pet.name')
                    ->searchable(),
                TextColumn::make('medicalRecord.id')
                    ->searchable(),
                TextColumn::make('uploadedBy.name')
                    ->label('Uploaded By')
                    ->sortable(),
                TextColumn::make('test_name')
                    ->searchable(),
                TextColumn::make('result_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('file_name')
                    ->label('Result File')
                    ->url(fn ($record): ?string => $record->file_path ? asset('storage/'.$record->file_path) : null)
                    ->openUrlInNewTab()
                    ->searchable(),
                TextColumn::make('file_size_bytes')
                    ->numeric()
                    ->label('File Size (Bytes)')
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
