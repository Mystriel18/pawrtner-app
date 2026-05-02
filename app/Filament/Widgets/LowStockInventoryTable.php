<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\InventoryItems\InventoryItemResource;
use App\Models\InventoryItem;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockInventoryTable extends TableWidget
{
    protected static ?string $heading = 'Low Stock Alerts';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => InventoryItem::query()
                ->where('is_active', true)
                ->whereColumn('quantity_on_hand', '<=', 'reorder_level')
                ->orderBy('quantity_on_hand'))
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category')
                    ->toggleable(),
                TextColumn::make('quantity_on_hand')
                    ->label('On Hand')
                    ->numeric(2)
                    ->badge()
                    ->color('danger')
                    ->sortable(),
                TextColumn::make('reorder_level')
                    ->label('Reorder Level')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('unit'),
                TextColumn::make('expires_at')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('manageItems')
                    ->label('Manage Inventory Items')
                    ->url(InventoryItemResource::getUrl())
                    ->icon('heroicon-o-squares-2x2'),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Open')
                    ->url(fn (InventoryItem $record): string => InventoryItemResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => false),
                ]),
            ]);
    }
}
