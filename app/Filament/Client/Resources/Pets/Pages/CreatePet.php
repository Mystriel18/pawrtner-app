<?php

namespace App\Filament\Client\Resources\Pets\Pages;

use App\Filament\Client\Resources\Pets\PetResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePet extends CreateRecord
{
    protected static string $resource = PetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_user_id'] = auth()->id();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
