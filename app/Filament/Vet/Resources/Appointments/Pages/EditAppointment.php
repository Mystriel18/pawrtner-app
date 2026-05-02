<?php

namespace App\Filament\Vet\Resources\Appointments\Pages;

use App\Filament\Concerns\ValidatesAppointmentScheduling;
use App\Filament\Vet\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentWorkflowService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;

class EditAppointment extends EditRecord
{
    use ValidatesAppointmentScheduling;

    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->validateAndPrepareAppointmentData($data, $this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->canTransitionTo(Appointment::STATUS_APPROVED))
                ->action(function (): void {
                    app(AppointmentWorkflowService::class)
                        ->transition($this->record, Appointment::STATUS_APPROVED, null, auth()->id());

                    $this->record->refresh();
                }),
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
                ->visible(fn (): bool => $this->record->canTransitionTo(Appointment::STATUS_DECLINED))
                ->action(function (array $data): void {
                    app(AppointmentWorkflowService::class)
                        ->transition($this->record, Appointment::STATUS_DECLINED, $data['note'] ?? null, auth()->id());

                    $this->record->refresh();
                }),
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
                ->visible(fn (): bool => $this->record->canTransitionTo(Appointment::STATUS_CANCELLED))
                ->action(function (array $data): void {
                    app(AppointmentWorkflowService::class)
                        ->transition($this->record, Appointment::STATUS_CANCELLED, $data['note'] ?? null, auth()->id());

                    $this->record->refresh();
                }),
            Action::make('complete')
                ->color('info')
                ->icon('heroicon-o-check-badge')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->canTransitionTo(Appointment::STATUS_COMPLETED))
                ->action(function (): void {
                    app(AppointmentWorkflowService::class)
                        ->transition($this->record, Appointment::STATUS_COMPLETED, null, auth()->id());

                    $this->record->refresh();
                }),
            DeleteAction::make(),
        ];
    }
}
