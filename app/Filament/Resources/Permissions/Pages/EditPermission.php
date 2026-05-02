<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (): void {
                    if (! $this->record->roles()->where('name', 'admin')->exists()) {
                        return;
                    }

                    throw ValidationException::withMessages([
                        'permission' => 'A permission assigned to the admin role cannot be deleted.',
                    ]);
                }),
        ];
    }
}