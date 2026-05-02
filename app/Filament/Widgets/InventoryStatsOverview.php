<?php

namespace App\Filament\Widgets;

use App\Models\InventoryItem;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Inventory Overview';

    protected function getStats(): array
    {
        $today = Carbon::today();
        $expiringWindowEnd = $today->copy()->addDays(30);

        $totalItems = InventoryItem::query()->count();
        $lowStockCount = InventoryItem::query()
            ->where('is_active', true)
            ->whereColumn('quantity_on_hand', '<=', 'reorder_level')
            ->count();
        $inactiveCount = InventoryItem::query()
            ->where('is_active', false)
            ->count();
        $expiringSoonCount = InventoryItem::query()
            ->where('is_active', true)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$today, $expiringWindowEnd])
            ->count();

        return [
            Stat::make('Total Items', (string) $totalItems)
                ->description('All inventory records')
                ->color('primary'),
            Stat::make('Low Stock', (string) $lowStockCount)
                ->description('At or below reorder level')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
            Stat::make('Inactive Items', (string) $inactiveCount)
                ->description('Currently disabled items')
                ->color($inactiveCount > 0 ? 'warning' : 'success'),
            Stat::make('Expiring in 30 Days', (string) $expiringSoonCount)
                ->description('Active items nearing expiry')
                ->color($expiringSoonCount > 0 ? 'warning' : 'success'),
        ];
    }
}
