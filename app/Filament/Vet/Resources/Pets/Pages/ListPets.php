<?php

namespace App\Filament\Vet\Resources\Pets\Pages;

use App\Filament\Vet\Resources\Pets\PetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPets extends ListRecords
{
    protected static string $resource = PetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
