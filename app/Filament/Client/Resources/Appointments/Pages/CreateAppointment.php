<?php

namespace App\Filament\Client\Resources\Appointments\Pages;

use App\Filament\Concerns\ValidatesAppointmentScheduling;
use App\Filament\Client\Resources\Appointments\AppointmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAppointment extends CreateRecord
{
    use ValidatesAppointmentScheduling;

    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['client_user_id'] = auth()->id();
        $data['status'] = 'pending';
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $this->validateAndPrepareAppointmentData($data);
    }
}
