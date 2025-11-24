<?php

namespace App\Filament\Widgets\Stats;

use App\Models\ConfiscatedItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperatorStatsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        return auth()->user()->role === 'operator_avsec';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Barang Dicatat Hari Ini', ConfiscatedItem::whereDate('created_at', today())->count())
                ->description('Jumlah barang yang Anda catat hari ini')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),
        ];
    }
}