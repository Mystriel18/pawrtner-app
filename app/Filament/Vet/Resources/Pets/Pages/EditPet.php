<?php

namespace App\Filament\Vet\Resources\Pets\Pages;

use App\Filament\Vet\Resources\Pets\PetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPet extends EditRecord
{
    protected static string $resource = PetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
