<?php

namespace App\Filament\Client\Resources\LabResults\Pages;

use App\Filament\Client\Resources\LabResults\LabResultResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLabResult extends EditRecord
{
    protected static string $resource = LabResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
