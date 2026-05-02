<?php

namespace App\Filament\Vet\Resources\Appointments\Schemas;

use App\Models\Appointment;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
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
                    ->default(fn (): ?int => auth()->id())
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('scheduled_at')
                    ->default(function (): ?string {
                        $scheduledAt = request()->query('scheduled_at');

                        if (! is_string($scheduledAt) || blank($scheduledAt)) {
                            return null;
                        }

                        try {
                            return now()->parse($scheduledAt)->format('Y-m-d H:i:s');
                        } catch (\Throwable) {
                            return null;
                        }
                    })
                    ->required(),
                TextInput::make('duration_minutes')
                    ->required()
                    ->numeric()
                    ->minValue(5)
                    ->default(30),
                Select::make('status')
                    ->options(Appointment::getStatusOptions())
                    ->default(Appointment::STATUS_PENDING)
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('reason')
                    ->label('Visit Reason'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Hidden::make('conflict_checked_at'),
                Hidden::make('created_by'),
                Hidden::make('updated_by'),
            ]);
    }
}
