<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'vet', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        $permissions = [
            'staff.manage',
            'roles.manage',
            'inventory.view',
            'inventory.manage',
            'financial.view',
            'financial.manage',
            'reports.view',
            'pets.viewAny',
            'pets.view',
            'pets.create',
            'pets.update',
            'pets.delete',
            'appointments.viewAny',
            'appointments.view',
            'appointments.create',
            'appointments.update',
            'appointments.delete',
            'medical_records.viewAny',
            'medical_records.view',
            'medical_records.create',
            'medical_records.update',
            'medical_records.delete',
            'vaccinations.viewAny',
            'vaccinations.view',
            'vaccinations.create',
            'vaccinations.update',
            'vaccinations.delete',
            'lab_results.viewAny',
            'lab_results.view',
            'lab_results.create',
            'lab_results.update',
            'lab_results.delete',
            'notifications.view',
            'notifications.manage',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::findByName('admin', 'web');
        $vetRole = Role::findByName('vet', 'web');
        $clientRole = Role::findByName('client', 'web');

        $adminRole->syncPermissions($permissions);

        $vetRole->syncPermissions([
            'pets.viewAny',
            'pets.view',
            'pets.create',
            'pets.update',
            'appointments.viewAny',
            'appointments.view',
            'appointments.update',
            'medical_records.viewAny',
            'medical_records.view',
            'medical_records.create',
            'medical_records.update',
            'vaccinations.viewAny',
            'vaccinations.view',
            'vaccinations.create',
            'vaccinations.update',
            'lab_results.viewAny',
            'lab_results.view',
            'lab_results.create',
            'lab_results.update',
            'notifications.view',
        ]);

        $clientRole->syncPermissions([
            'pets.view',
            'appointments.view',
            'appointments.create',
            'appointments.update',
            'medical_records.view',
            'vaccinations.view',
            'lab_results.view',
            'notifications.view',
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@pawrtner.test'],
            ['name' => 'Clinic Admin', 'password' => 'password']
        );
        $admin->assignRole('admin');

        $vet = User::firstOrCreate(
            ['email' => 'vet@pawrtner.test'],
            ['name' => 'Clinic Veterinarian', 'password' => 'password']
        );
        $vet->assignRole('vet');

        $client = User::firstOrCreate(
            ['email' => 'client@pawrtner.test'],
            ['name' => 'Clinic Client', 'password' => 'password']
        );
        $client->assignRole('client');

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Sample Client', 'password' => 'password']
        );
    }
}
