<?php

namespace App\Filament\Widgets\Charts;

use App\Models\ConfiscatedItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItemsByCategoryChart extends ChartWidget
{
    // REVISI: Semua properti ini harus NON-STATIC
    protected ?string $heading = 'Statistik Barang Sitaan (Per Kategori)';
    
    protected ?string $maxHeight = '300px';
    
    // Hapus kata 'static' di sini
    protected ?string $pollingInterval = '15s'; 

    
    public static function canView(): bool
    {
        // Pastikan user role sesuai dengan sistem Anda
        return in_array(auth()->user()->role, ['department_head_avsec', 'admin']);
    }

    // Dropdown Filter di pojok kanan atas grafik
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini',
            'all' => 'Semua Waktu',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $query = ConfiscatedItem::query();

        // Logika Filter
        match ($activeFilter) {
            'today' => $query->whereDate('confiscation_date', Carbon::today()),
            'week' => $query->whereBetween('confiscation_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
            'month' => $query->whereMonth('confiscation_date', Carbon::now()->month)->whereYear('confiscation_date', Carbon::now()->year),
            'year' => $query->whereYear('confiscation_date', Carbon::now()->year),
            default => $query,
        };

        $data = $query->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Barang',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#EF4444', // Red
                        '#F59E0B', // Amber
                        '#3B82F6', // Blue
                        '#10B981', // Green
                        '#8B5CF6', // Purple
                    ],
                    'borderWidth' => 0,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}