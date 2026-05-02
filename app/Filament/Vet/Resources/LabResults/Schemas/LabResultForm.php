<?php

namespace App\Filament\Vet\Resources\LabResults\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
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
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('medical_record_id')
                    ->relationship('medicalRecord', 'id'),
                Hidden::make('uploaded_by_user_id')
                    ->default(fn (): ?int => auth()->id()),
                TextInput::make('test_name')
                    ->required(),
                Textarea::make('result_summary')
                    ->columnSpanFull(),
                DatePicker::make('result_date'),
                FileUpload::make('file_path')
                    ->label('Lab Result File')
                    ->disk('public')
                    ->directory('lab-results')
                    ->storeFileNamesIn('file_name')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(10240)
                    ->columnSpanFull()
                    ->required(),
            ]);
    }
}
