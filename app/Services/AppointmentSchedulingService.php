<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AppointmentSchedulingService
{
    private const CLINIC_OPEN_HOUR = 8;
    private const CLINIC_CLOSE_HOUR = 18;

    public function validate(array $data, ?Appointment $ignoreAppointment = null): void
    {
        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $durationMinutes = max(5, (int) ($data['duration_minutes'] ?? 30));
        $appointmentEnd = $scheduledAt->copy()->addMinutes($durationMinutes);

        $errors = [];

        if ($scheduledAt->isSunday()) {
            $errors['scheduled_at'] = 'Appointments are not available on Sundays.';
        }

        $clinicOpen = $scheduledAt->copy()->setTime(self::CLINIC_OPEN_HOUR, 0);
        $clinicClose = $scheduledAt->copy()->setTime(self::CLINIC_CLOSE_HOUR, 0);

        if ($scheduledAt->lt($clinicOpen) || $appointmentEnd->gt($clinicClose)) {
            $errors['scheduled_at'] = sprintf(
                'Appointments must be within clinic hours (%02d:00 - %02d:00).',
                self::CLINIC_OPEN_HOUR,
                self::CLINIC_CLOSE_HOUR,
            );
        }

        if ($scheduledAt->lt(now()->subMinute())) {
            $errors['scheduled_at'] = 'Appointments cannot be scheduled in the past.';
        }

        $petId = (int) ($data['pet_id'] ?? 0);
        $veterinarianUserId = $data['veterinarian_user_id'] ?? null;

        $query = Appointment::query()
            ->whereIn('status', Appointment::ACTIVE_STATUSES)
            ->when($ignoreAppointment, fn ($q) => $q->whereKeyNot($ignoreAppointment->getKey()))
            ->where(function ($q) use ($petId, $veterinarianUserId): void {
                $q->where('pet_id', $petId);

                if (! empty($veterinarianUserId)) {
                    $q->orWhere('veterinarian_user_id', $veterinarianUserId);
                }
            })
            ->get(['id', 'pet_id', 'veterinarian_user_id', 'scheduled_at', 'duration_minutes']);

        foreach ($query as $existing) {
            $existingStart = Carbon::parse($existing->scheduled_at);
            $existingEnd = $existingStart->copy()->addMinutes((int) $existing->duration_minutes);

            $hasOverlap = $scheduledAt->lt($existingEnd) && $appointmentEnd->gt($existingStart);

            if (! $hasOverlap) {
                continue;
            }

            if ($existing->pet_id === $petId) {
                $errors['pet_id'] = 'This pet already has an overlapping appointment schedule.';
            }

            if (! empty($veterinarianUserId) && (int) $existing->veterinarian_user_id === (int) $veterinarianUserId) {
                $errors['veterinarian_user_id'] = 'Selected veterinarian is not available in this time slot.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }
}
