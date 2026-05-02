<?php

namespace App\Filament\Vet\Resources\Appointments\Pages;

use App\Filament\Vet\Resources\Appointments\AppointmentResource;
use Filament\Resources\Pages\ListRecords;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
