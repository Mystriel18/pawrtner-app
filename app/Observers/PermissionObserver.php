<?php

namespace App\Observers;

use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class PermissionObserver
{
    public function updating(Permission $permission): void
    {
        if (! $permission->isDirty('name')) {
            return;
        }

        if (! $this->isAssignedToAdminRole($permission)) {
            return;
        }

        throw ValidationException::withMessages([
            'name' => 'A permission assigned to the admin role cannot be renamed.',
        ]);
    }

    public function deleting(Permission $permission): void
    {
        if (! $this->isAssignedToAdminRole($permission)) {
            return;
        }

        throw ValidationException::withMessages([
            'permission' => 'A permission assigned to the admin role cannot be deleted.',
        ]);
    }

    protected function isAssignedToAdminRole(Permission $permission): bool
    {
        return $permission
            ->roles()
            ->where('name', 'admin')
            ->exists();
    }
}