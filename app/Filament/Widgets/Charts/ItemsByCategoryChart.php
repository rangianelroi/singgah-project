<?php

namespace App\Filament\Widgets\Charts;

use App\Models\ConfiscatedItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ItemsByCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Barang Sitaan Berdasarkan Kategori';

    protected ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 'full';

    /**
     * Widget ini hanya bisa dilihat oleh Dept Head dan Admin.
     */
    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['department_head_avsec', 'admin']);
    }

    protected function getData(): array
    {
        // Lakukan query ke database untuk menghitung jumlah item per kategori
        $data = ConfiscatedItem::query()
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->all();

        // Siapkan data untuk ditampilkan di grafik
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Barang',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#FF6384', // Dangerous Goods
                        '#36A2EB', // Prohibited Items
                        '#FFCE56', // Security Items
                        '#4BC0C0', // Other
                    ],
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Jenis grafik: bar, line, pie, etc.
    }
}