<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3 rounded-xl border border-gray-200/70 bg-white/70 p-4 dark:border-gray-700 dark:bg-gray-900/50">
            <a href="{{ $this->previousMonthUrl }}" class="fi-btn fi-btn-color-gray fi-size-sm">Previous</a>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $this->monthLabel }}</h2>
            <a href="{{ $this->nextMonthUrl }}" class="fi-btn fi-btn-color-gray fi-size-sm">Next</a>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-7">
            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                <div class="hidden md:block text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $dayName }}</div>
            @endforeach

            @foreach ($this->weeks as $week)
                @foreach ($week as $day)
                    <div class="min-h-40 rounded-xl border p-3 {{ $day['is_current_month'] ? 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900/40' : 'border-gray-100 bg-gray-50/70 dark:border-gray-800 dark:bg-gray-900/20' }}">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm font-semibold {{ $day['is_current_month'] ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-600' }}">
                                {{ $day['date']->format('j') }}
                            </span>
                            <div class="flex items-center gap-1">
                                @if (collect($day['events'])->contains(fn ($event) => $event['has_conflict']))
                                    <span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-500/20 dark:text-red-300">Conflict</span>
                                @endif
                                <a href="{{ $day['create_url'] }}"
                                   class="rounded-md border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-700/50 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50">
                                    + Create
                                </a>
                            </div>
                        </div>

                        <div class="space-y-2">
                            @forelse ($day['events'] as $event)
                                <a href="{{ $event['edit_url'] }}"
                                   class="block rounded-lg border p-2 text-xs transition hover:shadow-sm {{ $event['has_conflict'] ? 'border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900/60' }}">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $event['start']->format('g:i A') }} - {{ $event['end']->format('g:i A') }}
                                    </div>
                                    <div class="text-gray-600 dark:text-gray-300">{{ $event['pet_name'] }} · {{ $event['client_name'] }}</div>
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
                    </div>
                @endforeach
            @endforeach
        </div>

        <div class="rounded-xl border border-gray-200/70 bg-white/70 p-3 text-xs text-gray-600 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-300">
            Red cards indicate overlapping active appointments (pending/approved) that may cause schedule conflict.
        </div>
    </div>
</x-filament-panels::page>
