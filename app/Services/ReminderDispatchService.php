<?php

namespace App\Services;

use App\Filament\Client\Resources\Appointments\AppointmentResource as ClientAppointmentResource;
use App\Filament\Client\Resources\Vaccinations\VaccinationResource as ClientVaccinationResource;
use App\Models\Appointment;
use App\Models\NotificationLog;
use App\Models\User;
use App\Models\Vaccination;
use App\Notifications\ClientReminderNotification;
use Carbon\Carbon;
use Throwable;

class ReminderDispatchService
{
    public const TYPE_APPOINTMENT_UPCOMING = 'appointment_upcoming';
    public const TYPE_VACCINATION_DUE = 'vaccination_due';
    public const TYPE_APPOINTMENT_FOLLOW_UP = 'appointment_follow_up';

    /**
     * @return array<string, mixed>
     */
    public function dispatch(string $type = 'all', bool $isDryRun = false): array
    {
        $types = $this->resolveTypes($type);

        $summary = [
            'type' => $type,
            'dry_run' => $isDryRun,
            'candidates' => 0,
            'sent' => 0,
            'skipped' => 0,
            'failed' => 0,
            'by_type' => [],
        ];

        foreach ($types as $resolvedType) {
            $result = match ($resolvedType) {
                self::TYPE_APPOINTMENT_UPCOMING => $this->dispatchAppointmentUpcomingReminders($isDryRun),
                self::TYPE_VACCINATION_DUE => $this->dispatchVaccinationDueReminders($isDryRun),
                self::TYPE_APPOINTMENT_FOLLOW_UP => $this->dispatchAppointmentFollowUpReminders($isDryRun),
            };

            $summary['candidates'] += $result['candidates'];
            $summary['sent'] += $result['sent'];
            $summary['skipped'] += $result['skipped'];
            $summary['failed'] += $result['failed'];
            $summary['by_type'][$resolvedType] = $result;
        }

        return $summary;
    }

    /**
     * @return array<int, string>
     */
    private function resolveTypes(string $type): array
    {
        if ($type === 'all') {
            return [
                self::TYPE_APPOINTMENT_UPCOMING,
                self::TYPE_VACCINATION_DUE,
                self::TYPE_APPOINTMENT_FOLLOW_UP,
            ];
        }

        return match ($type) {
            self::TYPE_APPOINTMENT_UPCOMING,
            self::TYPE_VACCINATION_DUE,
            self::TYPE_APPOINTMENT_FOLLOW_UP => [$type],
            default => [],
        };
    }

    /**
     * @return array{candidates:int,sent:int,skipped:int,failed:int}
     */
    private function dispatchAppointmentUpcomingReminders(bool $isDryRun): array
    {
        $result = ['candidates' => 0, 'sent' => 0, 'skipped' => 0, 'failed' => 0];
        $now = now();

        $windows = [
            '24h' => [$now->copy()->addHours(23), $now->copy()->addHours(25)],
            '2h' => [$now->copy()->addMinutes(90), $now->copy()->addMinutes(150)],
        ];

        foreach ($windows as $windowLabel => [$start, $end]) {
            $appointments = Appointment::query()
                ->with(['pet', 'client'])
                ->whereIn('status', Appointment::ACTIVE_STATUSES)
                ->whereBetween('scheduled_at', [$start, $end])
                ->get();

            foreach ($appointments as $appointment) {
                $client = $appointment->client;

                if (! $client instanceof User || ! $client->hasRole('client')) {
                    continue;
                }

                $petName = $appointment->pet?->name ?? 'your pet';
                $scheduledAt = $appointment->scheduled_at;

                if (! $scheduledAt instanceof Carbon) {
                    continue;
                }

                $dedupKey = sprintf(
                    'reminder:appointment:%d:%s:user:%d:at:%s',
                    $appointment->id,
                    $windowLabel,
                    $client->id,
                    $scheduledAt->format('YmdHi'),
                );

                $dispatch = $this->dispatchSingleReminder(
                    recipient: $client,
                    notificationType: self::TYPE_APPOINTMENT_UPCOMING,
                    subject: sprintf('Upcoming appointment (%s)', $windowLabel),
                    body: sprintf(
                        'Reminder: %s has an appointment scheduled on %s.',
                        $petName,
                        $scheduledAt->format('M d, Y h:i A'),
                    ),
                    dedupKey: $dedupKey,
                    url: ClientAppointmentResource::getUrl(panel: 'client'),
                    meta: [
                        'window' => $windowLabel,
                        'appointment_id' => $appointment->id,
                        'scheduled_at' => $scheduledAt->toDateTimeString(),
                    ],
                    petId: $appointment->pet_id,
                    appointmentId: $appointment->id,
                    isDryRun: $isDryRun,
                );

                $result['candidates'] += $dispatch['candidates'];
                $result['sent'] += $dispatch['sent'];
                $result['skipped'] += $dispatch['skipped'];
                $result['failed'] += $dispatch['failed'];
            }
        }

        return $result;
    }

    /**
     * @return array{candidates:int,sent:int,skipped:int,failed:int}
     */
    private function dispatchVaccinationDueReminders(bool $isDryRun): array
    {
        $result = ['candidates' => 0, 'sent' => 0, 'skipped' => 0, 'failed' => 0];
        $today = now()->startOfDay();
        $upcomingWindowEnd = now()->addDays(7)->endOfDay();

        $vaccinations = Vaccination::query()
            ->with(['pet.owner'])
            ->whereNotNull('next_due_at')
            ->whereDate('next_due_at', '<=', $upcomingWindowEnd)
            ->get();

        foreach ($vaccinations as $vaccination) {
            $owner = $vaccination->pet?->owner;

            if (! $owner instanceof User || ! $owner->hasRole('client')) {
                continue;
            }

            $dueAt = $vaccination->next_due_at;

            if (! $dueAt instanceof Carbon) {
                continue;
            }

            $petName = $vaccination->pet?->name ?? 'your pet';
            $isOverdue = $dueAt->lt($today);
            $windowLabel = $isOverdue ? 'overdue' : 'due_soon';

            $dedupKey = sprintf(
                'reminder:vaccination:%d:%s:user:%d:due:%s',
                $vaccination->id,
                $windowLabel,
                $owner->id,
                $dueAt->format('Ymd'),
            );

            $dispatch = $this->dispatchSingleReminder(
                recipient: $owner,
                notificationType: self::TYPE_VACCINATION_DUE,
                subject: $isOverdue ? 'Vaccination overdue' : 'Vaccination due soon',
                body: $isOverdue
                    ? sprintf('%s has an overdue vaccination (%s) since %s.', $petName, $vaccination->vaccine_name, $dueAt->format('M d, Y'))
                    : sprintf('%s is due for %s on %s.', $petName, $vaccination->vaccine_name, $dueAt->format('M d, Y')),
                dedupKey: $dedupKey,
                url: ClientVaccinationResource::getUrl(panel: 'client'),
                meta: [
                    'window' => $windowLabel,
                    'vaccination_id' => $vaccination->id,
                    'next_due_at' => $dueAt->toDateString(),
                ],
                petId: $vaccination->pet_id,
                appointmentId: null,
                isDryRun: $isDryRun,
            );

            $result['candidates'] += $dispatch['candidates'];
            $result['sent'] += $dispatch['sent'];
            $result['skipped'] += $dispatch['skipped'];
            $result['failed'] += $dispatch['failed'];
        }

        return $result;
    }

    /**
     * @return array{candidates:int,sent:int,skipped:int,failed:int}
     */
    private function dispatchAppointmentFollowUpReminders(bool $isDryRun): array
    {
        $result = ['candidates' => 0, 'sent' => 0, 'skipped' => 0, 'failed' => 0];

        $windowStart = now()->subHours(26);
        $windowEnd = now()->subHours(22);

        $appointments = Appointment::query()
            ->with(['pet', 'client'])
            ->where('status', Appointment::STATUS_COMPLETED)
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        foreach ($appointments as $appointment) {
            $client = $appointment->client;

            if (! $client instanceof User || ! $client->hasRole('client')) {
                continue;
            }

            $petName = $appointment->pet?->name ?? 'your pet';

            $dedupKey = sprintf(
                'reminder:appointment-follow-up:%d:user:%d:completed:%s',
                $appointment->id,
                $client->id,
                $appointment->updated_at->format('YmdHi'),
            );

            $dispatch = $this->dispatchSingleReminder(
                recipient: $client,
                notificationType: self::TYPE_APPOINTMENT_FOLLOW_UP,
                subject: 'Post-visit follow-up',
                body: sprintf('How is %s doing after the recent clinic visit? Please update us if symptoms persist.', $petName),
                dedupKey: $dedupKey,
                url: ClientAppointmentResource::getUrl(panel: 'client'),
                meta: [
                    'appointment_id' => $appointment->id,
                    'completed_at' => optional($appointment->updated_at)->toDateTimeString(),
                ],
                petId: $appointment->pet_id,
                appointmentId: $appointment->id,
                isDryRun: $isDryRun,
            );

            $result['candidates'] += $dispatch['candidates'];
            $result['sent'] += $dispatch['sent'];
            $result['skipped'] += $dispatch['skipped'];
            $result['failed'] += $dispatch['failed'];
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array{candidates:int,sent:int,skipped:int,failed:int}
     */
    private function dispatchSingleReminder(
        User $recipient,
        string $notificationType,
        string $subject,
        string $body,
        string $dedupKey,
        string $url,
        array $meta,
        ?int $petId,
        ?int $appointmentId,
        bool $isDryRun,
    ): array {
        $result = ['candidates' => 1, 'sent' => 0, 'skipped' => 0, 'failed' => 0];

        $existing = NotificationLog::query()
            ->where('dedup_key', $dedupKey)
            ->exists();

        if ($existing) {
            $result['skipped'] = 1;

            return $result;
        }

        if ($isDryRun) {
            return $result;
        }

        $log = NotificationLog::query()->create([
            'user_id' => $recipient->id,
            'pet_id' => $petId,
            'appointment_id' => $appointmentId,
            'notification_type' => $notificationType,
            'channel' => 'in_app',
            'subject' => $subject,
            'body' => $body,
            'send_at' => now(),
            'status' => 'pending',
            'dedup_key' => $dedupKey,
            'meta' => $meta,
        ]);

        try {
            $recipient->notify(new ClientReminderNotification(
                title: $subject,
                body: $body,
                url: $url,
                reminderType: $notificationType,
                meta: $meta,
            ));

            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $result['sent'] = 1;
        } catch (Throwable $exception) {
            $log->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            $result['failed'] = 1;
        }

        return $result;
    }
}
