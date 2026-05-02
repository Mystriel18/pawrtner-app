<?php

namespace App\Policies;

use App\Models\Pet;
use App\Models\User;

class PetPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('pets.viewAny') || $user->hasRole('client');
    }

    public function view(User $user, Pet $pet): bool
    {
        if ($user->hasRole('vet')) {
            return $user->can('pets.view');
        }

        if ($user->hasRole('client')) {
            return $user->can('pets.view') && ((int) $pet->owner_user_id === (int) $user->id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('pets.create') || $user->hasRole('client');
    }

    public function update(User $user, Pet $pet): bool
    {
        if ($user->hasRole('vet')) {
            return $user->can('pets.update');
        }

        if ($user->hasRole('client')) {
            return (int) $pet->owner_user_id === (int) $user->id;
        }

        return false;
    }

    public function delete(User $user, Pet $pet): bool
    {
        if ($user->hasRole('client')) {
            return (int) $pet->owner_user_id === (int) $user->id;
        }

        return $user->can('pets.delete');
    }
}
