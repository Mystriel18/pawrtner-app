<?php

namespace App\Filament\Client\Resources\Pets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
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
                FileUpload::make('profile_photo_path')
                    ->label('Pet Photo')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('400')
                    ->imageResizeTargetHeight('400')
                    ->disk('public')
                    ->directory('pet-photos')
                    ->visibility('public')
                    ->columnSpanFull()
                    ->nullable(),
                Hidden::make('owner_user_id')
                    ->default(fn (): ?int => auth()->id())
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('species')
                    ->options([
                        'dog' => 'Dog',
                        'cat' => 'Cat',
                        'bird' => 'Bird',
                        'rabbit' => 'Rabbit',
                        'hamster' => 'Hamster',
                        'guinea_pig' => 'Guinea Pig',
                        'reptile' => 'Reptile',
                        'fish' => 'Fish',
                        'other' => 'Other',
                    ])
                    ->searchable()
                    ->required(),
                TextInput::make('breed'),
                Select::make('sex')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'unknown' => 'Unknown',
                    ])
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
                Hidden::make('created_by')
                    ->default(fn (): ?int => auth()->id()),
                Hidden::make('updated_by')
                    ->default(fn (): ?int => auth()->id()),
            ]);
    }
}

