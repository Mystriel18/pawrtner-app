<?php

namespace App\Filament\Widgets;

use App\Models\FinancialRecord;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Financial Overview';

    protected function getStats(): array
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        $monthIncome = (float) FinancialRecord::query()
            ->where('entry_type', 'income')
            ->where('status', 'posted')
            ->whereBetween('occurred_on', [$monthStart, $monthEnd])
            ->sum('amount');

        $monthExpense = (float) FinancialRecord::query()
            ->where('entry_type', 'expense')
            ->where('status', 'posted')
            ->whereBetween('occurred_on', [$monthStart, $monthEnd])
            ->sum('amount');

        $pendingCount = FinancialRecord::query()
            ->where('status', 'pending')
            ->count();

        $netMonth = $monthIncome - $monthExpense;

        return [
            Stat::make('Income (This Month)', number_format($monthIncome, 2))
                ->description('Posted income entries')
                ->color('success'),
            Stat::make('Expense (This Month)', number_format($monthExpense, 2))
                ->description('Posted expense entries')
                ->color('danger'),
            Stat::make('Net (This Month)', number_format($netMonth, 2))
                ->description('Income minus expenses')
                ->color($netMonth >= 0 ? 'success' : 'danger'),
            Stat::make('Pending Entries', (string) $pendingCount)
                ->description('Requires posting review')
                ->color($pendingCount > 0 ? 'warning' : 'success'),
        ];
    }
}
