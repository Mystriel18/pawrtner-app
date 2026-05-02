<?php

namespace App\Filament\Client\Resources\LabResults\Pages;

use App\Filament\Client\Resources\LabResults\LabResultResource;
use Filament\Resources\Pages\ListRecords;

class ListLabResults extends ListRecords
{
    protected static string $resource = LabResultResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
