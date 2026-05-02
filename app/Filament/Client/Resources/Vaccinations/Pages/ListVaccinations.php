<?php

namespace App\Filament\Client\Resources\Vaccinations\Pages;

use App\Filament\Client\Resources\Vaccinations\VaccinationResource;
use Filament\Resources\Pages\ListRecords;

class ListVaccinations extends ListRecords
{
    protected static string $resource = VaccinationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
