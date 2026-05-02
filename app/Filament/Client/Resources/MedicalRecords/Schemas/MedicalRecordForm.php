<?php

namespace App\Filament\Client\Resources\MedicalRecords\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MedicalRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pet_id')
                    ->relationship('pet', 'name')
                    ->required(),
                TextInput::make('veterinarian_user_id')
                    ->numeric(),
                Select::make('appointment_id')
                    ->relationship('appointment', 'id'),
                DatePicker::make('visit_date')
                    ->required(),
                TextInput::make('chief_complaint'),
                Textarea::make('diagnosis')
                    ->columnSpanFull(),
                Textarea::make('treatment')
                    ->columnSpanFull(),
                Textarea::make('prescription')
                    ->columnSpanFull(),
                TextInput::make('weight_kg')
                    ->numeric(),
                TextInput::make('temperature_c')
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('updated_by')
                    ->numeric(),
            ]);
    }
}
