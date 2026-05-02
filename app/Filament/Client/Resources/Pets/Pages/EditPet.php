<?php

namespace App\Filament\Client\Resources\Pets\Pages;

use App\Filament\Client\Resources\Pets\PetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPet extends EditRecord
{
    protected static string $resource = PetResource::class;

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
