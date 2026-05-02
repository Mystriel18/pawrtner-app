<?php

use App\Services\ReminderDispatchService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reminders:dispatch {--type=all} {--dry-run}', function (ReminderDispatchService $reminders): int {
    $type = (string) $this->option('type');
    $dryRun = (bool) $this->option('dry-run');
    $supportedTypes = [
        'all',
        ReminderDispatchService::TYPE_APPOINTMENT_UPCOMING,
        ReminderDispatchService::TYPE_VACCINATION_DUE,
        ReminderDispatchService::TYPE_APPOINTMENT_FOLLOW_UP,
    ];

    if (! in_array($type, $supportedTypes, true)) {
        $this->error(sprintf('Invalid --type value [%s]. Supported: %s', $type, implode(', ', $supportedTypes)));

        return self::FAILURE;
    }

    $summary = $reminders->dispatch($type, $dryRun);

    $this->info(sprintf(
        'Reminder dispatch completed (type=%s, dry_run=%s): candidates=%d, sent=%d, skipped=%d, failed=%d',
        $summary['type'],
        $summary['dry_run'] ? 'yes' : 'no',
        $summary['candidates'],
        $summary['sent'],
        $summary['skipped'],
        $summary['failed'],
    ));

    foreach ($summary['by_type'] as $resolvedType => $result) {
        $this->line(sprintf(
            ' - %s => candidates=%d, sent=%d, skipped=%d, failed=%d',
            $resolvedType,
            $result['candidates'],
            $result['sent'],
            $result['skipped'],
            $result['failed'],
        ));
    }

    return self::SUCCESS;
})->purpose('Dispatch in-app reminder notifications for appointments and vaccinations.');

Schedule::command('reminders:dispatch --type=appointment_upcoming')->hourly();
Schedule::command('reminders:dispatch --type=appointment_follow_up')->hourlyAt(15);
Schedule::command('reminders:dispatch --type=vaccination_due')->dailyAt('08:00');
