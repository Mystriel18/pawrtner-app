<?php

namespace App\Filament\Vet\Widgets;

use App\Models\Appointment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VetAppointmentStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Vet Appointment Snapshot';

    protected function getStats(): array
    {
        $vetId = auth()->id();

        $baseQuery = Appointment::query()->where('veterinarian_user_id', $vetId);

        $pendingApprovals = (clone $baseQuery)
            ->where('status', Appointment::STATUS_PENDING)
            ->count();

        $todayAppointments = (clone $baseQuery)
            ->whereIn('status', Appointment::ACTIVE_STATUSES)
            ->whereDate('scheduled_at', now()->toDateString())
            ->count();

        $completedToday = (clone $baseQuery)
            ->where('status', Appointment::STATUS_COMPLETED)
            ->whereDate('updated_at', now()->toDateString())
            ->count();

        return [
            Stat::make('Pending Approvals', (string) $pendingApprovals)
                ->description('Requires vet decision')
                ->color($pendingApprovals > 0 ? 'warning' : 'success'),
            Stat::make('Today\'s Active Appointments', (string) $todayAppointments)
                ->description('Pending or approved')
                ->color('info'),
            Stat::make('Completed Today', (string) $completedToday)
                ->description('Marked as completed')
                ->color('success'),
        ];
    }
}
