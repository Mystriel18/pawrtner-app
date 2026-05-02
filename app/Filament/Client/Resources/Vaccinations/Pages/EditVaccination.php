<?php

namespace App\Filament\Client\Resources\Vaccinations\Pages;

use App\Filament\Client\Resources\Vaccinations\VaccinationResource;
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
