<?php

namespace App\Filament\Widgets;

use App\Models\NotificationLog;
use App\Services\ReminderDispatchService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReminderDispatchOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Reminder Dispatch Overview';

    protected function getStats(): array
    {
        $last24Hours = now()->subDay();
        $reminderTypes = [
            ReminderDispatchService::TYPE_APPOINTMENT_UPCOMING,
            ReminderDispatchService::TYPE_VACCINATION_DUE,
            ReminderDispatchService::TYPE_APPOINTMENT_FOLLOW_UP,
        ];

        $sent24h = NotificationLog::query()
            ->whereIn('notification_type', $reminderTypes)
            ->where('status', 'sent')
            ->where('sent_at', '>=', $last24Hours)
            ->count();

        $failed24h = NotificationLog::query()
            ->whereIn('notification_type', $reminderTypes)
            ->where('status', 'failed')
            ->where('updated_at', '>=', $last24Hours)
            ->count();

        $skipped24h = NotificationLog::query()
            ->whereIn('notification_type', $reminderTypes)
            ->where('status', 'skipped')
            ->where('updated_at', '>=', $last24Hours)
            ->count();

        $pending = NotificationLog::query()
            ->whereIn('notification_type', $reminderTypes)
            ->where('status', 'pending')
            ->count();

        return [
            Stat::make('Sent (24h)', (string) $sent24h)
                ->description('Reminder notifications delivered')
                ->color($sent24h > 0 ? 'success' : 'gray'),
            Stat::make('Failed (24h)', (string) $failed24h)
                ->description('Dispatch failures in last 24 hours')
                ->color($failed24h > 0 ? 'danger' : 'success'),
            Stat::make('Skipped (24h)', (string) $skipped24h)
                ->description('Dedup / already dispatched reminders')
                ->color($skipped24h > 0 ? 'warning' : 'gray'),
            Stat::make('Pending', (string) $pending)
                ->description('Queued reminder logs awaiting send')
                ->color($pending > 0 ? 'warning' : 'success'),
        ];
    }
}
