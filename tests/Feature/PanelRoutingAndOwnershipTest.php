<?php

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function createPanelTestUserWithRole(string $roleName): User
{
    $role = Role::query()->firstOrCreate([
        'name' => $roleName,
        'guard_name' => 'web',
    ]);

    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

function assertPanelBlockedResponse(TestResponse $response): void
{
    expect($response->status())->toBeIn([302, 403]);
}

test('users can access only their own panel dashboard', function () {
    $admin = createPanelTestUserWithRole('admin');
    $vet = createPanelTestUserWithRole('vet');
    $client = createPanelTestUserWithRole('client');

    $this->actingAs($admin);
    $this->get('/admin')->assertOk();
    assertPanelBlockedResponse($this->get('/vet'));
    assertPanelBlockedResponse($this->get('/client'));

    $this->actingAs($vet);
    $this->get('/vet')->assertOk();
    assertPanelBlockedResponse($this->get('/admin'));
    assertPanelBlockedResponse($this->get('/client'));

    $this->actingAs($client);
    $this->get('/client')->assertOk();
    assertPanelBlockedResponse($this->get('/admin'));
    assertPanelBlockedResponse($this->get('/vet'));
});

test('client appointments list shows only owned appointments', function () {
    $clientA = createPanelTestUserWithRole('client');
    $clientB = createPanelTestUserWithRole('client');

    $petA = Pet::query()->create([
        'owner_user_id' => $clientA->id,
        'name' => 'Alpha',
        'species' => 'dog',
        'sex' => 'male',
        'is_active' => true,
    ]);

    $petB = Pet::query()->create([
        'owner_user_id' => $clientB->id,
        'name' => 'Beta',
        'species' => 'cat',
        'sex' => 'female',
        'is_active' => true,
    ]);

    Appointment::query()->create([
        'pet_id' => $petA->id,
        'client_user_id' => $clientA->id,
        'scheduled_at' => now()->addDay(),
        'duration_minutes' => 30,
        'status' => Appointment::STATUS_PENDING,
        'reason' => 'Owned checkup marker',
    ]);

    Appointment::query()->create([
        'pet_id' => $petB->id,
        'client_user_id' => $clientB->id,
        'scheduled_at' => now()->addDays(2),
        'duration_minutes' => 30,
        'status' => Appointment::STATUS_PENDING,
        'reason' => 'Foreign checkup marker',
    ]);

    $this->actingAs($clientA);

    $response = $this->get('/client/appointments');

    $response->assertOk();
    $response->assertSee('Owned checkup marker');
    $response->assertDontSee('Foreign checkup marker');
});

test('client appointment calendar shows only owned pets', function () {
    $clientA = createPanelTestUserWithRole('client');
    $clientB = createPanelTestUserWithRole('client');

    $petA = Pet::query()->create([
        'owner_user_id' => $clientA->id,
        'name' => 'Calendar Owned Pet',
        'species' => 'dog',
        'sex' => 'male',
        'is_active' => true,
    ]);

    $petB = Pet::query()->create([
        'owner_user_id' => $clientB->id,
        'name' => 'Calendar Foreign Pet',
        'species' => 'cat',
        'sex' => 'female',
        'is_active' => true,
    ]);

    Appointment::query()->create([
        'pet_id' => $petA->id,
        'client_user_id' => $clientA->id,
        'scheduled_at' => now()->addDays(3),
        'duration_minutes' => 30,
        'status' => Appointment::STATUS_PENDING,
        'reason' => 'Owned calendar event',
    ]);

    Appointment::query()->create([
        'pet_id' => $petB->id,
        'client_user_id' => $clientB->id,
        'scheduled_at' => now()->addDays(3),
        'duration_minutes' => 30,
        'status' => Appointment::STATUS_PENDING,
        'reason' => 'Foreign calendar event',
    ]);

    $this->actingAs($clientA);

    $response = $this->get('/client/appointment-calendar');

    $response->assertOk();
    $response->assertSee('Calendar Owned Pet');
    $response->assertDontSee('Calendar Foreign Pet');
});
