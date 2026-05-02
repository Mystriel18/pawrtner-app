<?php

namespace App\Filament\Vet\Resources\MedicalRecords\Pages;

use App\Filament\Vet\Resources\MedicalRecords\MedicalRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMedicalRecords extends ListRecords
{
    protected static string $resource = MedicalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
