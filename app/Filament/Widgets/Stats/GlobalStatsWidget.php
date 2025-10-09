<?php

namespace App\Filament\Widgets\Stats;

use App\Models\ConfiscatedItem;
use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GlobalStatsWidget extends BaseWidget
{
    /**
     * Widget ini hanya bisa dilihat oleh Dept Head dan Admin.
     */
    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['department_head_avsec', 'admin']);
    }

    protected function getStats(): array
    {
        // === STATISTIK 1: Total Penyitaan Bulan Ini ===
        $totalThisMonth = ConfiscatedItem::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // === STATISTIK 2: Total Barang Dikirim (Status SHIPPED) ===
        $totalShipped = ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
            $query->where('status', 'SHIPPED');
        })->count();

        // === STATISTIK 3: Total Barang Dimusnahkan (Status DISPOSED) ===
        $totalDisposed = ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
            $query->where('status', 'DISPOSED');
        })->count();

        return [
            Stat::make('Penyitaan Bulan Ini', $totalThisMonth)
                ->description('Total barang yang dicatat bulan ini')
                ->descriptionIcon('heroicon-m-archive-box-arrow-down')
                ->color('primary'),
            Stat::make('Total Barang Terkirim', $totalShipped)
                ->description('Semua barang yang telah dikirim')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),
            Stat::make('Total Barang Dimusnahkan', $totalDisposed)
                ->description('Semua barang yang telah dimusnahkan')
                ->descriptionIcon('heroicon-m-fire')
                ->color('danger'),
        ];
    }
}