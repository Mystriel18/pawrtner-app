<?php

namespace App\Filament\Vet\Resources\LabResults\Pages;

use App\Filament\Vet\Resources\LabResults\LabResultResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLabResults extends ListRecords
{
    protected static string $resource = LabResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
