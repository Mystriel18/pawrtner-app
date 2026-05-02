<?php

namespace App\Filament\Vet\Resources\Vaccinations\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VaccinationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pet_id')
                    ->relationship('pet', 'name')
                    ->required(),
                Select::make('medical_record_id')
                    ->relationship('medicalRecord', 'id'),
                Select::make('administered_by_user_id')
                    ->label('Administered By')
                    ->options(fn () => User::role('vet')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                TextInput::make('vaccine_name')
                    ->required(),
                TextInput::make('manufacturer'),
                TextInput::make('batch_number'),
                DatePicker::make('administered_at')
                    ->required(),
                DatePicker::make('next_due_at'),
                TextInput::make('status')
                    ->required()
                    ->default('up_to_date'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('updated_by')
                    ->numeric(),
            ]);
    }
}
