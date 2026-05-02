<?php

namespace App\Filament\Vet\Pages;

use App\Filament\Vet\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Pages\Page;
use UnitEnum;

class AppointmentCalendar extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Appointment Calendar';

    protected static ?string $title = 'Appointment Calendar';

    protected static ?string $slug = 'appointment-calendar';

    protected static ?int $navigationSort = 2;

    protected static string | UnitEnum | null $navigationGroup = 'Appointments';

    protected string $view = 'filament.vet.pages.appointment-calendar';

    public string $month;

    /**
     * @var array<int, array<int, array<string, mixed>>>
     */
    public array $weeks = [];

    public string $monthLabel;

    public string $previousMonthUrl;

    public string $nextMonthUrl;

    public function mount(): void
    {
        $requestedMonth = request()->query('month');

        if (is_string($requestedMonth) && preg_match('/^\d{4}-\d{2}$/', $requestedMonth) === 1) {
            $this->month = $requestedMonth;
        } else {
            $this->month = now()->format('Y-m');
        }

        $this->buildCalendarData();
    }

    protected function buildCalendarData(): void
    {
        $monthStart = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $gridStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        $appointments = Appointment::query()
            ->with(['pet', 'client'])
            ->where('veterinarian_user_id', auth()->id())
            ->whereBetween('scheduled_at', [$gridStart, $gridEnd])
            ->orderBy('scheduled_at')
            ->get();

        $byDate = [];

        foreach ($appointments as $appointment) {
            $start = Carbon::parse($appointment->scheduled_at);
            $duration = max(5, (int) ($appointment->duration_minutes ?? 30));
            $end = $start->copy()->addMinutes($duration);
            $dateKey = $start->toDateString();

            $byDate[$dateKey][] = [
                'id' => $appointment->id,
                'start' => $start,
                'end' => $end,
                'status' => $appointment->status,
                'status_label' => Appointment::getStatusOptions()[$appointment->status] ?? ucfirst($appointment->status),
                'pet_name' => $appointment->pet?->name ?? 'Unknown pet',
                'client_name' => $appointment->client?->name ?? 'Unknown client',
                'edit_url' => AppointmentResource::getUrl('edit', ['record' => $appointment], panel: 'vet'),
                'has_conflict' => false,
            ];
        }

        foreach ($byDate as $dateKey => $events) {
            usort($events, fn (array $a, array $b): int => $a['start']->getTimestamp() <=> $b['start']->getTimestamp());

            $activeIndexes = [];

            foreach ($events as $index => $event) {
                foreach ($activeIndexes as $activeIndex) {
                    $activeEvent = $events[$activeIndex];

                    $isOverlapping = $event['start']->lt($activeEvent['end']) && $event['end']->gt($activeEvent['start']);

                    if (! $isOverlapping) {
                        continue;
                    }

                    $events[$index]['has_conflict'] = true;
                    $events[$activeIndex]['has_conflict'] = true;
                }

                if (in_array($event['status'], Appointment::ACTIVE_STATUSES, true)) {
                    $activeIndexes[] = $index;
                }
            }

            $byDate[$dateKey] = $events;
        }

        $cursor = $gridStart->copy();
        $weeks = [];

        while ($cursor->lte($gridEnd)) {
            $week = [];

            for ($day = 0; $day < 7; $day++) {
                $dateKey = $cursor->toDateString();

                $week[] = [
                    'date' => $cursor->copy(),
                    'is_current_month' => $cursor->month === $monthStart->month,
                    'create_url' => AppointmentResource::getUrl(
                        'create',
                        ['scheduled_at' => $cursor->copy()->setTime(9, 0)->format('Y-m-d H:i:s')],
                        panel: 'vet',
                    ),
                    'events' => $byDate[$dateKey] ?? [],
                ];

                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        $this->weeks = $weeks;
        $this->monthLabel = $monthStart->format('F Y');
        $this->previousMonthUrl = static::getUrl(['month' => $monthStart->copy()->subMonth()->format('Y-m')], panel: 'vet');
        $this->nextMonthUrl = static::getUrl(['month' => $monthStart->copy()->addMonth()->format('Y-m')], panel: 'vet');
    }
}
