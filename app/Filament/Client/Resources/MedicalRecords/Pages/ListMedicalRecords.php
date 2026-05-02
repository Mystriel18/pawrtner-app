<?php

namespace App\Filament\Client\Resources\MedicalRecords\Pages;

use App\Filament\Client\Resources\MedicalRecords\MedicalRecordResource;
use Filament\Resources\Pages\ListRecords;

class ListMedicalRecords extends ListRecords
{
    protected static string $resource = MedicalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
