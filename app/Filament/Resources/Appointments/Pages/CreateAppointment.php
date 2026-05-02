<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Concerns\ValidatesAppointmentScheduling;
use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAppointment extends CreateRecord
{
    use ValidatesAppointmentScheduling;

    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $this->validateAndPrepareAppointmentData($data);
    }
}
