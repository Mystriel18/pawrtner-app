<?php

namespace App\Filament\Vet\Resources\Appointments\Tables;

use App\Models\Appointment;
use App\Services\AppointmentWorkflowService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pet.name')
                    ->searchable(),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('veterinarian.name')
                    ->label('Veterinarian')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Appointment::getStatusOptions()[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        Appointment::STATUS_PENDING => 'warning',
                        Appointment::STATUS_APPROVED => 'success',
                        Appointment::STATUS_DECLINED => 'danger',
                        Appointment::STATUS_CANCELLED => 'gray',
                        Appointment::STATUS_COMPLETED => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('reason')
                    ->label('Visit Reason')
                    ->searchable(),
                TextColumn::make('conflict_checked_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn (Appointment $record): bool => $record->canTransitionTo(Appointment::STATUS_APPROVED))
                    ->action(fn (Appointment $record) => app(AppointmentWorkflowService::class)
                        ->transition($record, Appointment::STATUS_APPROVED, null, auth()->id())),
                Action::make('decline')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->schema([
                        Textarea::make('note')
                            ->label('Decline reason')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->requiresConfirmation()
                    ->visible(fn (Appointment $record): bool => $record->canTransitionTo(Appointment::STATUS_DECLINED))
                    ->action(fn (Appointment $record, array $data) => app(AppointmentWorkflowService::class)
                        ->transition($record, Appointment::STATUS_DECLINED, $data['note'] ?? null, auth()->id())),
                Action::make('cancel')
                    ->color('gray')
                    ->icon('heroicon-o-no-symbol')
                    ->schema([
                        Textarea::make('note')
                            ->label('Cancellation reason')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->requiresConfirmation()
                    ->visible(fn (Appointment $record): bool => $record->canTransitionTo(Appointment::STATUS_CANCELLED))
                    ->action(fn (Appointment $record, array $data) => app(AppointmentWorkflowService::class)
                        ->transition($record, Appointment::STATUS_CANCELLED, $data['note'] ?? null, auth()->id())),
                Action::make('complete')
                    ->color('info')
                    ->icon('heroicon-o-check-badge')
                    ->requiresConfirmation()
                    ->visible(fn (Appointment $record): bool => $record->canTransitionTo(Appointment::STATUS_COMPLETED))
                    ->action(fn (Appointment $record) => app(AppointmentWorkflowService::class)
                        ->transition($record, Appointment::STATUS_COMPLETED, null, auth()->id())),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
