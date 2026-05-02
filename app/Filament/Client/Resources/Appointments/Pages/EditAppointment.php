<?php

namespace App\Filament\Client\Resources\Appointments\Pages;

use App\Filament\Concerns\ValidatesAppointmentScheduling;
use App\Filament\Client\Resources\Appointments\AppointmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAppointment extends EditRecord
{
    use ValidatesAppointmentScheduling;

    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['client_user_id'] = auth()->id();

        if (! in_array($data['status'] ?? 'pending', ['pending', 'confirmed', 'cancelled', 'completed'], true)) {
            $data['status'] = 'pending';
        }

        return $this->validateAndPrepareAppointmentData($data, $this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
