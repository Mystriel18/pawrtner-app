<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function createUserWithRole(string $roleName): User
{
    $role = Role::query()->firstOrCreate([
        'name' => $roleName,
        'guard_name' => 'web',
    ]);

    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

test('admin can access role and permission management pages', function () {
    $admin = createUserWithRole('admin');

    $this->actingAs($admin);

    $this->get('/admin/roles')->assertOk();
    $this->get('/admin/permissions')->assertOk();
});

test('non-admin cannot access role and permission management pages', function () {
    $client = createUserWithRole('client');

    $this->actingAs($client);

    $rolesResponse = $this->get('/admin/roles');
    $permissionsResponse = $this->get('/admin/permissions');

    expect([$rolesResponse->status(), $permissionsResponse->status()])
        ->each->toBeIn([302, 403]);
});

test('guest is redirected to admin login for governance pages', function () {
    $this->get('/admin/roles')->assertRedirect('/admin/login');
    $this->get('/admin/permissions')->assertRedirect('/admin/login');
});
