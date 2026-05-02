<?php

namespace App\Filament\Vet\Resources\Vaccinations\Pages;

use App\Filament\Vet\Resources\Vaccinations\VaccinationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVaccination extends EditRecord
{
    protected static string $resource = VaccinationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
