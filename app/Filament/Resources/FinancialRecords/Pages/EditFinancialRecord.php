<?php

namespace App\Filament\Resources\FinancialRecords\Pages;

use App\Filament\Resources\FinancialRecords\FinancialRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinancialRecord extends EditRecord
{
    protected static string $resource = FinancialRecordResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
