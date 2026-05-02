<?php

namespace App\Filament\Client\Resources\MedicalRecords\Pages;

use App\Filament\Client\Resources\MedicalRecords\MedicalRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMedicalRecord extends EditRecord
{
    protected static string $resource = MedicalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
