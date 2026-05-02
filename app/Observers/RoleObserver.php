<?php

namespace App\Observers;

use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class RoleObserver
{
    public function updating(Role $role): void
    {
        if (! $role->isDirty('name')) {
            return;
        }

        if ($role->getOriginal('name') !== 'admin') {
            return;
        }

        throw ValidationException::withMessages([
            'name' => 'The admin role name cannot be changed.',
        ]);
    }

    public function deleting(Role $role): void
    {
        if ($role->name !== 'admin') {
            return;
        }

        throw ValidationException::withMessages([
            'role' => 'The admin role cannot be deleted.',
        ]);
    }
}