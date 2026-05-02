<?php

namespace App\Filament\Resources\FinancialRecords\Pages;

use App\Filament\Resources\FinancialRecords\FinancialRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFinancialRecord extends CreateRecord
{
    protected static string $resource = FinancialRecordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $actorId = auth()->id();

        $data['recorded_by_user_id'] = $data['recorded_by_user_id'] ?? $actorId;
        $data['created_by'] = $actorId;
        $data['updated_by'] = $actorId;

        return $data;
    }
}
