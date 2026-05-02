<?php

namespace App\Filament\Resources\InventoryMovements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inventoryItem.name')
                    ->label('Item')
                    ->searchable(),
                TextColumn::make('movement_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'stock_in' => 'success',
                        'stock_out' => 'danger',
                        'adjustment' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('quantity_before')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('quantity_after')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('movedBy.name')
                    ->label('Moved By')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reference_type')
                    ->searchable(),
                TextColumn::make('reference_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reason')
                    ->searchable(),
                TextColumn::make('moved_at')
                    ->dateTime()
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
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
