<?php

namespace App\Filament\Widgets\Charts;

use App\Models\ConfiscatedItem;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ConfiscatedItemsTrendChart extends ChartWidget
{
    protected ?string $heading = 'Tren Penyitaan Barang (Tahun Ini)';
    protected ?string $maxHeight = '300px';
    
    // Urutkan chart ini agar muncul di paling atas dashboard
    protected static ?int $sort = 1; 

    public static function canView(): bool
    {
        // Pastikan user role sesuai dengan sistem Anda
        return in_array(auth()->user()->role, ['department_head_avsec', 'admin']);
    }

    protected function getData(): array
    {
        // Menggunakan package Flowframe Trend (bawaan Filament)
        $data = Trend::model(ConfiscatedItem::class)
            ->dateColumn('confiscation_date')
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Barang Disita',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3B82F6', // Biru
                    'fill' => true,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}