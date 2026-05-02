<?php

namespace App\Filament\Client\Widgets;

use App\Models\Appointment;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingAppointmentsWidget extends TableWidget
{
    protected static ?string $heading = 'Upcoming Appointments';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Appointment::query()
                ->where('client_user_id', auth()->id())
                ->whereIn('status', Appointment::ACTIVE_STATUSES)
                ->where('scheduled_at', '>=', now())
                ->orderBy('scheduled_at'))
            ->columns([
                TextColumn::make('pet.name')
                    ->label('Pet')
                    ->searchable(),
                TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Appointment::getStatusOptions()[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        Appointment::STATUS_PENDING => 'warning',
                        Appointment::STATUS_APPROVED => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('reason')
                    ->label('Visit Reason')
                    ->placeholder('-'),
            ])
            ->headerActions([
                Action::make('openAppointments')
                    ->label('Open My Appointments')
                    ->icon('heroicon-o-calendar-days')
                    ->url(url('/client/appointments')),
            ])
            ->recordActions([])
            ->toolbarActions([])
            ->defaultPaginationPageOption(5);
    }
}
