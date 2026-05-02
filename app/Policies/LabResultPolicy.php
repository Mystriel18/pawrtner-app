<?php

namespace App\Policies;

use App\Models\LabResult;
use App\Models\User;

class LabResultPolicy
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
        return $user->can('lab_results.viewAny') || $user->hasRole('client');
    }

    public function view(User $user, LabResult $labResult): bool
    {
        if ($user->hasRole('vet')) {
            return $user->can('lab_results.view');
        }

        if ($user->hasRole('client')) {
            $ownsPet = (int) optional($labResult->pet)->owner_user_id === (int) $user->id;

            return $user->can('lab_results.view') && $ownsPet;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('lab_results.create');
    }

    public function update(User $user, LabResult $labResult): bool
    {
        if ($user->hasRole('vet')) {
            return $user->can('lab_results.update');
        }

        return false;
    }

    public function delete(User $user, LabResult $labResult): bool
    {
        return $user->can('lab_results.delete');
    }
}
