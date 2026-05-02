<?php

namespace App\Filament\Concerns;

use App\Models\Appointment;
use App\Services\AppointmentSchedulingService;

trait ValidatesAppointmentScheduling
{
    protected function validateAndPrepareAppointmentData(array $data, ?Appointment $record = null): array
    {
        app(AppointmentSchedulingService::class)->validate($data, $record);

        $data['conflict_checked_at'] = now();
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
