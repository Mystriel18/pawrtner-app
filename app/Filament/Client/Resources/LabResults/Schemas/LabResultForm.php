<?php

namespace App\Filament\Client\Resources\LabResults\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LabResultForm
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
                TextInput::make('uploaded_by_user_id')
                    ->numeric(),
                TextInput::make('test_name')
                    ->required(),
                Textarea::make('result_summary')
                    ->columnSpanFull(),
                DatePicker::make('result_date'),
                TextInput::make('file_path')
                    ->required(),
                TextInput::make('file_name')
                    ->required(),
                TextInput::make('mime_type'),
                TextInput::make('file_size_bytes')
                    ->numeric(),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('updated_by')
                    ->numeric(),
            ]);
    }
}
