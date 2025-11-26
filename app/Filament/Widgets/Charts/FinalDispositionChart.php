<?php

namespace App\Filament\Widgets\Charts;

use App\Models\ConfiscatedItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FinalDispositionChart extends ChartWidget
{
    // PERBAIKAN: Hapus kata 'static' pada heading dan maxHeight
    protected ?string $heading = 'Persentase Akhir Nasib Barang';
    
    protected ?string $maxHeight = '300px';
    
    // Note: $sort tetap static karena digunakan oleh sistem layout dashboard sebelum widget di-load
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        // Ambil status terakhir dari log
        $data = ConfiscatedItem::query()
            ->join('item_status_logs', function($join) {
                $join->on('confiscated_items.id', '=', 'item_status_logs.item_id')
                     ->whereRaw('item_status_logs.created_at = (select max(created_at) from item_status_logs where item_id = confiscated_items.id)');
            })
            // Filter hanya status "Final"
            ->whereIn('item_status_logs.status', ['DISPOSED', 'PICKED_UP', 'SHIPPED', 'HANDED_TO_POLICE'])
            ->select('item_status_logs.status', DB::raw('count(*) as total'))
            ->groupBy('item_status_logs.status')
            ->pluck('total', 'status')
            ->all();

        // Mapping label agar lebih enak dibaca (Translate kode status ke Bahasa Indonesia)
        $labels = array_map(function($status) {
            return match($status) {
                'DISPOSED' => 'Dimusnahkan',
                'PICKED_UP' => 'Diambil Penumpang',
                'SHIPPED' => 'Dikirim Ekspedisi',
                'HANDED_TO_POLICE' => 'Diserahterimakan Polisi',
                default => $status
            };
        }, array_keys($data));

        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#EF4444', // Merah (Disposed)
                        '#10B981', // Hijau (Picked Up)
                        '#3B82F6', // Biru (Shipped)
                        '#8B5CF6', // Ungu (Police)
                    ],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}