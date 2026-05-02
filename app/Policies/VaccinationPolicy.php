<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vaccination;

class VaccinationPolicy
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
        return $user->can('vaccinations.viewAny') || $user->hasRole('client');
    }

    public function view(User $user, Vaccination $vaccination): bool
    {
        if ($user->hasRole('vet')) {
            return $user->can('vaccinations.view');
        }

        if ($user->hasRole('client')) {
            $ownsPet = (int) optional($vaccination->pet)->owner_user_id === (int) $user->id;

            return $user->can('vaccinations.view') && $ownsPet;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('vaccinations.create');
    }

    public function update(User $user, Vaccination $vaccination): bool
    {
        if ($user->hasRole('vet')) {
            return $user->can('vaccinations.update');
        }

        return false;
    }

    public function delete(User $user, Vaccination $vaccination): bool
    {
        return $user->can('vaccinations.delete');
    }
}
