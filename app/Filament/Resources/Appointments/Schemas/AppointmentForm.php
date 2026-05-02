<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pet_id')
                    ->relationship('pet', 'name')
                    ->required(),
                Select::make('client_user_id')
                    ->label('Client')
                    ->options(fn () => User::role('client')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                Select::make('veterinarian_user_id')
                    ->label('Veterinarian')
                    ->options(fn () => User::role('vet')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('scheduled_at')
                    ->required(),
                TextInput::make('duration_minutes')
                    ->required()
                    ->numeric()
                    ->default(30),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('reason'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                DateTimePicker::make('conflict_checked_at'),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('updated_by')
                    ->numeric(),
            ]);
    }
}
