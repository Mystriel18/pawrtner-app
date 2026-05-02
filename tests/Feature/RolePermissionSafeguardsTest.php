<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('admin role cannot be renamed', function () {
    $adminRole = Role::query()->create([
        'name' => 'admin',
        'guard_name' => 'web',
    ]);

    expect(function () use ($adminRole): void {
        $adminRole->update(['name' => 'super-admin']);
    })->toThrow(ValidationException::class, 'admin role name cannot be changed');
});

test('admin role cannot be deleted', function () {
    $adminRole = Role::query()->create([
        'name' => 'admin',
        'guard_name' => 'web',
    ]);

    expect(function () use ($adminRole): void {
        $adminRole->delete();
    })->toThrow(ValidationException::class, 'admin role cannot be deleted');
});

test('permission assigned to admin role cannot be renamed', function () {
    $adminRole = Role::query()->create([
        'name' => 'admin',
        'guard_name' => 'web',
    ]);

    $permission = Permission::query()->create([
        'name' => 'users.manage',
        'guard_name' => 'web',
    ]);

    $adminRole->givePermissionTo($permission);

    expect(function () use ($permission): void {
        $permission->update(['name' => 'users.override']);
    })->toThrow(ValidationException::class, 'cannot be renamed');
});

test('non-admin role and unassigned permission remain editable', function () {
    $vetRole = Role::query()->create([
        'name' => 'vet',
        'guard_name' => 'web',
    ]);

    $permission = Permission::query()->create([
        'name' => 'appointments.view',
        'guard_name' => 'web',
    ]);

    $vetRole->update(['name' => 'veterinarian']);
    $permission->update(['name' => 'appointments.read']);

    expect($vetRole->fresh()?->name)->toBe('veterinarian');
    expect($permission->fresh()?->name)->toBe('appointments.read');

    expect($permission->delete())->toBeTrue();
    expect($vetRole->delete())->toBeTrue();
});
