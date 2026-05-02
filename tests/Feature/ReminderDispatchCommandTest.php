<?php

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\NotificationLog;
use App\Models\Pet;
use App\Models\User;
use App\Models\Vaccination;
use App\Services\ReminderDispatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('reminders dispatch command runs in dry-run mode', function () {
    $this->artisan('reminders:dispatch --dry-run')
        ->assertExitCode(0);
});

test('reminders dispatch command rejects unsupported type', function () {
    $this->artisan('reminders:dispatch --type=unknown')
        ->assertExitCode(1);
});

test('appointment upcoming reminders are deduplicated across repeated runs', function () {
    $clientRole = Role::query()->firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

    $client = User::factory()->create();
    $client->assignRole($clientRole);

    $pet = Pet::query()->create([
        'owner_user_id' => $client->id,
        'name' => 'Bantay',
        'species' => 'dog',
        'sex' => 'male',
        'is_active' => true,
    ]);

    Appointment::query()->create([
        'pet_id' => $pet->id,
        'client_user_id' => $client->id,
        'scheduled_at' => now()->addHours(24),
        'duration_minutes' => 30,
        'status' => Appointment::STATUS_APPROVED,
        'reason' => 'Checkup',
    ]);

    $this->artisan('reminders:dispatch --type=' . ReminderDispatchService::TYPE_APPOINTMENT_UPCOMING)
        ->assertExitCode(0);
    $this->artisan('reminders:dispatch --type=' . ReminderDispatchService::TYPE_APPOINTMENT_UPCOMING)
        ->assertExitCode(0);

    expect(NotificationLog::query()
        ->where('notification_type', ReminderDispatchService::TYPE_APPOINTMENT_UPCOMING)
        ->count())->toBe(1);
});

test('vaccination due reminders are deduplicated across repeated runs', function () {
    $clientRole = Role::query()->firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

    $client = User::factory()->create();
    $client->assignRole($clientRole);

    $pet = Pet::query()->create([
        'owner_user_id' => $client->id,
        'name' => 'Ming',
        'species' => 'cat',
        'sex' => 'female',
        'is_active' => true,
    ]);

    $record = MedicalRecord::query()->create([
        'pet_id' => $pet->id,
        'visit_date' => now()->toDateString(),
        'chief_complaint' => 'Routine vaccination',
    ]);

    Vaccination::query()->create([
        'pet_id' => $pet->id,
        'medical_record_id' => $record->id,
        'vaccine_name' => 'Rabies',
        'administered_at' => now()->subMonths(11)->toDateString(),
        'next_due_at' => now()->addDays(3)->toDateString(),
        'status' => 'up_to_date',
    ]);

    $this->artisan('reminders:dispatch --type=' . ReminderDispatchService::TYPE_VACCINATION_DUE)
        ->assertExitCode(0);
    $this->artisan('reminders:dispatch --type=' . ReminderDispatchService::TYPE_VACCINATION_DUE)
        ->assertExitCode(0);

    expect(NotificationLog::query()
        ->where('notification_type', ReminderDispatchService::TYPE_VACCINATION_DUE)
        ->count())->toBe(1);
});
