<?php

namespace App\Filament\Resources\InventoryItems\Pages;

use App\Filament\Resources\InventoryItems\InventoryItemResource;
use App\Services\InventoryMovementService;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryItem extends CreateRecord
{
    protected static string $resource = InventoryItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $actorId = Auth::id();

        $data['created_by'] = $actorId;
        $data['updated_by'] = $actorId;

        return $data;
    }

    protected function afterCreate(): void
    {
        app(InventoryMovementService::class)
            ->recordInitialStock($this->record, Auth::id());
    }
}
