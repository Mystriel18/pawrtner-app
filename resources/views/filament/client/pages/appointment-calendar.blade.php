<x-filament-panels::page>
    @php
        $gridClass = match ($this->viewMode) {
            'month' => 'md:grid-cols-7',
            'week' => 'md:grid-cols-2 xl:grid-cols-7',
            default => '',
        };
    @endphp

    <div class="space-y-4">
        <div class="flex flex-col gap-3 rounded-xl border border-gray-200/70 bg-white/70 p-4 dark:border-gray-700 dark:bg-gray-900/50">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ $this->previousMonthUrl }}" class="fi-btn fi-btn-color-gray fi-size-sm">Previous</a>
                <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $this->monthLabel }}</h2>
                <a href="{{ $this->nextMonthUrl }}" class="fi-btn fi-btn-color-gray fi-size-sm">Next</a>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="inline-flex rounded-lg border border-gray-200 bg-white p-1 dark:border-gray-700 dark:bg-gray-900/60">
                    <a href="{{ $this->monthViewUrl }}"
                       class="rounded-md px-3 py-1 text-xs font-semibold transition {{ $this->viewMode === 'month' ? 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                        Month
                    </a>
                    <a href="{{ $this->weekViewUrl }}"
                       class="rounded-md px-3 py-1 text-xs font-semibold transition {{ $this->viewMode === 'week' ? 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                        Week
                    </a>
                    <a href="{{ $this->dayViewUrl }}"
                       class="rounded-md px-3 py-1 text-xs font-semibold transition {{ $this->viewMode === 'day' ? 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                        Day
                    </a>
                </div>

                <a href="{{ $this->todayUrl }}" class="fi-btn fi-btn-color-gray fi-size-sm">Today</a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 {{ $gridClass }}">
            @if ($this->viewMode !== 'day')
                @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                    <div class="hidden text-center text-xs font-semibold uppercase tracking-wide text-gray-500 md:block dark:text-gray-400">{{ $dayName }}</div>
                @endforeach
            @endif

            @foreach ($this->weeks as $week)
                @foreach ($week as $day)
                    <div class="{{ $this->viewMode === 'day' ? 'mx-auto w-full max-w-4xl' : '' }} min-h-40 rounded-xl border p-3 {{ $day['is_current_month'] ? 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900/40' : 'border-gray-100 bg-gray-50/70 dark:border-gray-800 dark:bg-gray-900/20' }}">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm font-semibold {{ $day['is_current_month'] ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-600' }}">
                                {{ $this->viewMode === 'day' ? $day['date']->format('D, M j') : $day['date']->format('j') }}
                            </span>
                            <div class="flex items-center gap-1">
                                @if (collect($day['events'])->contains(fn ($event) => $event['has_conflict']))
                                    <span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-500/20 dark:text-red-300">Conflict</span>
                                @endif
                                <a href="{{ $day['create_url'] }}"
                                   class="rounded-md border border-pink-200 bg-pink-50 px-2 py-0.5 text-[10px] font-semibold text-pink-700 transition hover:bg-pink-100 dark:border-pink-700/50 dark:bg-pink-900/30 dark:text-pink-300 dark:hover:bg-pink-900/50">
                                    + Create
                                </a>
                            </div>
                        </div>

                        @if ($this->viewMode === 'day')
                            @php
                                $groupedEvents = collect($day['events'])->groupBy(fn (array $event) => $event['start']->format('g A'));
                            @endphp

                            <div class="max-h-[65vh] space-y-3 overflow-y-auto pr-1">
                                @forelse ($groupedEvents as $slotLabel => $slotEvents)
                                    <section class="space-y-2">
                                        <div class="sticky top-0 z-10 whitespace-nowrap rounded-md border border-gray-200/80 bg-white/95 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-gray-600 backdrop-blur dark:border-gray-700 dark:bg-gray-900/95 dark:text-gray-300">
                                            {{ $slotLabel }}
                                        </div>

                                        @foreach ($slotEvents as $event)
                                            <a href="{{ $event['edit_url'] }}"
                                               class="block rounded-lg border p-3 text-sm transition hover:shadow-sm {{ $event['has_conflict'] ? 'border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900/60' }}">
                                                <div class="sm:flex sm:items-center sm:justify-between sm:gap-3">
                                                    <div class="font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ $event['start']->format('g:i A') }} - {{ $event['end']->format('g:i A') }}
                                                    </div>
                                                    <div class="mt-1 inline-flex rounded-full px-2 py-0.5 text-xs font-medium sm:mt-0
                                                        {{ $event['status'] === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300' : '' }}
                                                        {{ $event['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300' : '' }}
                                                        {{ $event['status'] === 'completed' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' : '' }}
                                                        {{ $event['status'] === 'declined' ? 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300' : '' }}
                                                        {{ in_array($event['status'], ['cancelled'], true) ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' : '' }}
                                                    ">
                                                        {{ $event['status_label'] }}
                                                    </div>
                                                </div>
                                                <div class="mt-1 text-gray-600 dark:text-gray-300">{{ $event['pet_name'] }} · {{ $event['veterinarian_name'] }}</div>
                                                <div class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium
                                                    {{ $event['status'] === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300' : '' }}
                                                    {{ $event['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300' : '' }}
                                                    {{ $event['status'] === 'completed' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' : '' }}
                                                    {{ $event['status'] === 'declined' ? 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300' : '' }}
                                                    {{ in_array($event['status'], ['cancelled'], true) ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' : '' }}
                                                ">
                                                    {{ $event['has_conflict'] ? 'Potential conflict' : 'Scheduled' }}
                                                </div>
                                            </a>
                                        @endforeach
                                    </section>
                                @empty
                                    <div class="text-xs text-gray-400 dark:text-gray-500">No appointments</div>
                                @endforelse
                            </div>
                        @else
                            <div class="space-y-2">
                                @forelse ($day['events'] as $event)
                                    <a href="{{ $event['edit_url'] }}"
                                       class="block rounded-lg border p-2 text-xs transition hover:shadow-sm {{ $event['has_conflict'] ? 'border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900/60' }}">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $event['start']->format('g:i A') }} - {{ $event['end']->format('g:i A') }}
                                        </div>
                                        <div class="text-gray-600 dark:text-gray-300">{{ $event['pet_name'] }} · {{ $event['veterinarian_name'] }}</div>
                                        <div class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium
                                            {{ $event['status'] === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300' : '' }}
                                            {{ $event['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300' : '' }}
                                            {{ $event['status'] === 'completed' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' : '' }}
                                            {{ $event['status'] === 'declined' ? 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300' : '' }}
                                            {{ in_array($event['status'], ['cancelled'], true) ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' : '' }}
                                        ">
                                            {{ $event['status_label'] }}
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-xs text-gray-400 dark:text-gray-500">No appointments</div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                @endforeach
            @endforeach
        </div>

        <div class="rounded-xl border border-gray-200/70 bg-white/70 p-3 text-xs text-gray-600 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-300">
            Calendar is scoped to your appointments only. Red cards indicate overlapping active appointments (pending/approved).
        </div>
    </div>
</x-filament-panels::page>
