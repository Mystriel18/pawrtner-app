<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
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
        return $user->can('medical_records.viewAny') || $user->hasRole('client');
    }

    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        if ($user->hasRole('vet')) {
            return $user->can('medical_records.view');
        }

        if ($user->hasRole('client')) {
            $ownsPet = (int) optional($medicalRecord->pet)->owner_user_id === (int) $user->id;

            return $user->can('medical_records.view') && $ownsPet;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('medical_records.create');
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        if ($user->hasRole('vet')) {
            return $user->can('medical_records.update');
        }

        return false;
    }

    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->can('medical_records.delete');
    }
}
