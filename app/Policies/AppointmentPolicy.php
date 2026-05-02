<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
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
        return $user->can('appointments.viewAny') || $user->hasRole('client');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('vet')) {
            return $user->can('appointments.view');
        }

        if ($user->hasRole('client')) {
            $ownsByClient = (int) $appointment->client_user_id === (int) $user->id;
            $ownsByPet = (int) optional($appointment->pet)->owner_user_id === (int) $user->id;

            return $user->can('appointments.view') && ($ownsByClient || $ownsByPet);
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->hasRole('vet')) {
            return false;
        }

        return $user->can('appointments.create');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('vet')) {
            return $user->can('appointments.update');
        }

        if ($user->hasRole('client')) {
            $ownsByClient = (int) $appointment->client_user_id === (int) $user->id;
            $ownsByPet = (int) optional($appointment->pet)->owner_user_id === (int) $user->id;

            return $user->can('appointments.update') && ($ownsByClient || $ownsByPet);
        }

        return false;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->can('appointments.delete');
    }
}
