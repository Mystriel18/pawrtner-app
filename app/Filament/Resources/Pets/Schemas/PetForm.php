<?php

namespace App\Filament\Resources\Pets\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('owner_user_id')
                    ->label('Owner (Client)')
                    ->options(fn () => User::role('client')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('species')
                    ->required(),
                TextInput::make('breed'),
                TextInput::make('sex')
                    ->required()
                    ->default('unknown'),
                DatePicker::make('birth_date'),
                TextInput::make('color'),
                TextInput::make('microchip_number'),
                TextInput::make('weight_kg')
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('updated_by')
                    ->numeric(),
            ]);
    }
}
