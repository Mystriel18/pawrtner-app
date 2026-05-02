<?php

namespace App\Filament\Client\Resources\Vaccinations\Pages;

use App\Filament\Client\Resources\Vaccinations\VaccinationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVaccination extends CreateRecord
{
    protected static string $resource = VaccinationResource::class;
}
