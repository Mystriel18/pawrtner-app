<?php

namespace App\Filament\Client\Resources\Appointments\Schemas;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pet_id')
                    ->relationship(
                        name: 'pet',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('owner_user_id', auth()->id()),
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                Hidden::make('client_user_id')
                    ->default(fn (): ?int => auth()->id()),
                Select::make('veterinarian_user_id')
                    ->label('Veterinarian')
                    ->options(fn () => User::role('vet')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('scheduled_at')
                    ->default(function (): ?string {
                        $scheduledAt = request()->query('scheduled_at');

                        if (! is_string($scheduledAt) || blank($scheduledAt)) {
                            return null;
                        }

                        try {
                            return Carbon::parse($scheduledAt)->format('Y-m-d H:i:s');
                        } catch (\Throwable) {
                            return null;
                        }
                    })
                    ->required(),
                TextInput::make('duration_minutes')
                    ->required()
                    ->numeric()
                    ->default(30),
                Select::make('status')
                    ->options(Appointment::getStatusOptions())
                    ->required()
                    ->default(Appointment::STATUS_PENDING)
                    ->disabled(),
                TextInput::make('reason'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Hidden::make('created_by')
                    ->default(fn (): ?int => auth()->id()),
                Hidden::make('updated_by')
                    ->default(fn (): ?int => auth()->id()),
            ]);
    }
}
