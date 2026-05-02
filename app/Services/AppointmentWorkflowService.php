<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\NotificationLog;
use App\Models\User;
use App\Notifications\AppointmentStatusChangedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentWorkflowService
{
    public function transition(Appointment $appointment, string $targetStatus, ?string $note = null, ?int $actorId = null): Appointment
    {
        $note = filled($note) ? trim($note) : null;

        if (! $appointment->canTransitionTo($targetStatus)) {
            throw ValidationException::withMessages([
                'status' => sprintf('Cannot change appointment status from "%s" to "%s".', $appointment->status, $targetStatus),
            ]);
        }

        if (in_array($targetStatus, [Appointment::STATUS_DECLINED, Appointment::STATUS_CANCELLED], true) && blank($note)) {
            throw ValidationException::withMessages([
                'note' => 'A reason is required for this status update.',
            ]);
        }

        $fromStatus = $appointment->status;

        DB::transaction(function () use ($appointment, $targetStatus, $note, $actorId): void {
            $appointment->status = $targetStatus;
            $appointment->updated_by = $actorId;

            if ($note) {
                $statusNotes = sprintf('[%s] %s', strtoupper($targetStatus), $note);
                $appointment->notes = filled($appointment->notes)
                    ? $appointment->notes . PHP_EOL . $statusNotes
                    : $statusNotes;
            }

            $appointment->save();
        });

        $appointment->loadMissing(['pet', 'client', 'veterinarian']);
        $actor = $actorId ? User::find($actorId) : null;

        $this->notifyRelatedUsers($appointment, $fromStatus, $targetStatus, $note, $actorId, $actor);

        return $appointment;
    }

    private function notifyRelatedUsers(
        Appointment $appointment,
        string $fromStatus,
        string $toStatus,
        ?string $note,
        ?int $actorId,
        ?User $actor,
    ): void {
        $recipients = collect([$appointment->client, $appointment->veterinarian])
            ->filter(fn ($user) => $user instanceof User)
            ->unique('id');

        foreach ($recipients as $recipient) {
            $dedupKey = sprintf('appointment:%d:status:%s:user:%d', $appointment->id, $toStatus, $recipient->id);

            $log = NotificationLog::query()->firstOrCreate(
                ['dedup_key' => $dedupKey],
                [
                    'user_id' => $recipient->id,
                    'pet_id' => $appointment->pet_id,
                    'appointment_id' => $appointment->id,
                    'notification_type' => 'appointment_status_changed',
                    'channel' => 'in_app',
                    'subject' => 'Appointment status updated',
                    'body' => sprintf(
                        'Appointment for %s is now %s.',
                        $appointment->pet?->name ?? 'your pet',
                        Appointment::getStatusOptions()[$toStatus] ?? ucfirst($toStatus),
                    ),
                    'send_at' => now(),
                    'sent_at' => now(),
                    'status' => 'sent',
                    'meta' => [
                        'from_status' => $fromStatus,
                        'to_status' => $toStatus,
                        'note' => $note,
                        'actor_id' => $actorId,
                    ],
                    'created_by' => $actorId,
                    'updated_by' => $actorId,
                ],
            );

            if (! $log->wasRecentlyCreated) {
                continue;
            }

            $recipient->notify(new AppointmentStatusChangedNotification($appointment, $toStatus, $note, $actor));
        }
    }
}
