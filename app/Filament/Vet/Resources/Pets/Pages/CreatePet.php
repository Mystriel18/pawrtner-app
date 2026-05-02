<?php

namespace App\Filament\Vet\Resources\Pets\Pages;

use App\Filament\Vet\Resources\Pets\PetResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePet extends CreateRecord
{
    protected static string $resource = PetResource::class;
}
