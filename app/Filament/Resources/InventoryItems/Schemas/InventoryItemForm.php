<?php

namespace App\Filament\Resources\InventoryItems\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class InventoryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')
                    ->label('SKU'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('category'),
                Select::make('unit')
                    ->options([
                        'pcs' => 'pcs',
                        'box' => 'box',
                        'pack' => 'pack',
                        'bottle' => 'bottle',
                        'tablet' => 'tablet',
                        'ml' => 'ml',
                        'g' => 'g',
                    ])
                    ->required()
                    ->default('pcs'),
                TextInput::make('quantity_on_hand')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                TextInput::make('reorder_level')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                TextInput::make('cost_amount')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('price_amount')
                    ->numeric()
                    ->minValue(0),
                DatePicker::make('expires_at'),
                Toggle::make('is_active')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
