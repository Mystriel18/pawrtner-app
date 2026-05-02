<?php

namespace App\Filament\Client\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);

        $user->assignRole('client');

        return $user;
    }
}
