<?php

namespace App\Filament\Vet\Resources\Vaccinations\Pages;

use App\Filament\Vet\Resources\Vaccinations\VaccinationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVaccination extends CreateRecord
{
    protected static string $resource = VaccinationResource::class;
}
