<?php

namespace App\Filament\Client\Pages;

use App\Filament\Client\Resources\Appointments\AppointmentResource;
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

    protected string $view = 'filament.client.pages.appointment-calendar';

    public string $viewMode;

    public string $month;

    public string $focusDate;

    /**
     * @var array<int, array<int, array<string, mixed>>>
     */
    public array $weeks = [];

    public string $monthLabel;

    public string $previousMonthUrl;

    public string $nextMonthUrl;

    public string $todayUrl;

    public string $monthViewUrl;

    public string $weekViewUrl;

    public string $dayViewUrl;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('client') === true;
    }

    public function mount(): void
    {
        $requestedViewMode = request()->query('view');
        $requestedMonth = request()->query('month');
        $requestedDate = request()->query('date');

        if (is_string($requestedViewMode) && in_array($requestedViewMode, ['month', 'week', 'day'], true)) {
            $this->viewMode = $requestedViewMode;
        } else {
            $this->viewMode = 'month';
        }

        if (is_string($requestedDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $requestedDate) === 1) {
            $this->focusDate = $requestedDate;
        } else {
            $this->focusDate = now()->toDateString();
        }

        if (is_string($requestedMonth) && preg_match('/^\d{4}-\d{2}$/', $requestedMonth) === 1) {
            $this->month = $requestedMonth;
            if ($this->viewMode === 'month') {
                $this->focusDate = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth()->toDateString();
            }
        } else {
            $this->month = Carbon::createFromFormat('Y-m-d', $this->focusDate)->format('Y-m');
        }

        $this->buildCalendarData();
    }

    protected function buildCalendarData(): void
    {
        $focusDate = Carbon::createFromFormat('Y-m-d', $this->focusDate)->startOfDay();

        if ($this->viewMode === 'week') {
            $gridStart = $focusDate->copy()->startOfWeek(Carbon::MONDAY);
            $gridEnd = $focusDate->copy()->endOfWeek(Carbon::SUNDAY);
            $this->monthLabel = 'Week of '.$gridStart->format('M j, Y');

            $previousFocusDate = $gridStart->copy()->subWeek()->toDateString();
            $nextFocusDate = $gridStart->copy()->addWeek()->toDateString();
        } elseif ($this->viewMode === 'day') {
            $gridStart = $focusDate->copy();
            $gridEnd = $focusDate->copy();
            $this->monthLabel = $focusDate->format('l, F j, Y');

            $previousFocusDate = $focusDate->copy()->subDay()->toDateString();
            $nextFocusDate = $focusDate->copy()->addDay()->toDateString();
        } else {
            $monthStart = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $gridStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
            $gridEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);
            $this->monthLabel = $monthStart->format('F Y');

            $previousFocusDate = $monthStart->copy()->subMonth()->startOfMonth()->toDateString();
            $nextFocusDate = $monthStart->copy()->addMonth()->startOfMonth()->toDateString();
        }

        $appointments = Appointment::query()
            ->with(['pet', 'veterinarian'])
            ->where(function ($query): void {
                $query
                    ->where('client_user_id', auth()->id())
                    ->orWhereHas('pet', function ($petQuery): void {
                        $petQuery->where('owner_user_id', auth()->id());
                    });
            })
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
                'veterinarian_name' => $appointment->veterinarian?->name ?? 'Unassigned',
                'edit_url' => AppointmentResource::getUrl('edit', ['record' => $appointment], panel: 'client'),
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

            $daysInCurrentRow = $this->viewMode === 'day' ? 1 : 7;

            for ($day = 0; $day < $daysInCurrentRow; $day++) {
                $dateKey = $cursor->toDateString();

                $week[] = [
                    'date' => $cursor->copy(),
                    'is_current_month' => $this->viewMode !== 'month' || $cursor->month === Carbon::createFromFormat('Y-m', $this->month)->month,
                    'create_url' => AppointmentResource::getUrl(
                        'create',
                        ['scheduled_at' => $cursor->copy()->setTime(9, 0)->format('Y-m-d H:i:s')],
                        panel: 'client',
                    ),
                    'events' => $byDate[$dateKey] ?? [],
                ];

                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        $this->weeks = $weeks;

        $this->previousMonthUrl = static::getUrl($this->buildQueryParams($previousFocusDate), panel: 'client');
        $this->nextMonthUrl = static::getUrl($this->buildQueryParams($nextFocusDate), panel: 'client');
        $this->todayUrl = static::getUrl($this->buildQueryParams(now()->toDateString()), panel: 'client');
        $this->monthViewUrl = static::getUrl([
            'view' => 'month',
            'month' => Carbon::createFromFormat('Y-m-d', $this->focusDate)->format('Y-m'),
            'date' => Carbon::createFromFormat('Y-m-d', $this->focusDate)->startOfMonth()->toDateString(),
        ], panel: 'client');
        $this->weekViewUrl = static::getUrl([
            'view' => 'week',
            'date' => $this->focusDate,
        ], panel: 'client');
        $this->dayViewUrl = static::getUrl([
            'view' => 'day',
            'date' => $this->focusDate,
        ], panel: 'client');
    }

    /**
     * @return array<string, string>
     */
    protected function buildQueryParams(string $focusDate): array
    {
        if ($this->viewMode === 'month') {
            return [
                'view' => 'month',
                'month' => Carbon::createFromFormat('Y-m-d', $focusDate)->format('Y-m'),
                'date' => Carbon::createFromFormat('Y-m-d', $focusDate)->startOfMonth()->toDateString(),
            ];
        }

        return [
            'view' => $this->viewMode,
            'date' => $focusDate,
        ];
    }
}
