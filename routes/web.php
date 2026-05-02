<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();

    if (! $user) {
        return view('welcome');
    }

    if ($user->hasRole('admin')) {
        return redirect()->route('filament.admin.pages.dashboard');
    }

    if ($user->hasRole('vet')) {
        return redirect()->route('filament.vet.pages.dashboard');
    }

    if ($user->hasRole('client')) {
        return redirect()->route('filament.client.pages.dashboard');
    }

    return view('welcome');
});
