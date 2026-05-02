<?php

namespace App\Filament\Client\Resources\Pets\Pages;

use App\Filament\Client\Resources\Pets\PetResource;
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
