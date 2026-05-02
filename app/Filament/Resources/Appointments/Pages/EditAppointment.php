<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Concerns\ValidatesAppointmentScheduling;
use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Actions\DeleteAction;
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
            DeleteAction::make(),
        ];
    }
}
