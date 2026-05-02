<?php

namespace App\Filament\Resources\InventoryItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('unit')
                    ->searchable(),
                TextColumn::make('quantity_on_hand')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record): string => (float) $record->quantity_on_hand <= (float) $record->reorder_level ? 'danger' : 'success'),
                TextColumn::make('reorder_level')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cost_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->date()
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
                Filter::make('low_stock')
                    ->label('Low stock only')
                    ->query(fn (Builder $query): Builder => $query->whereColumn('quantity_on_hand', '<=', 'reorder_level')),
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
