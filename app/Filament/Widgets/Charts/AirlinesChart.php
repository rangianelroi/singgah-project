<?php

namespace App\Filament\Widgets\Charts;

use App\Models\ConfiscatedItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AirlinesChart extends ChartWidget
{
    protected ?string $heading = 'Maskapai dengan Sitaan';
    protected ?string $maxHeight = '300px';

    public static function canView(): bool
    {
        // Pastikan user role sesuai dengan sistem Anda
        return in_array(auth()->user()->role, ['department_head_avsec']);
    }

    protected function getData(): array
    {
        // Join dari Item -> Flight -> Airline
        $data = ConfiscatedItem::query()
            ->join('flights', 'confiscated_items.flight_id', '=', 'flights.id')
            ->join('airlines', 'flights.airline_id', '=', 'airlines.id')
            ->select('airlines.name', DB::raw('count(*) as total'))
            ->groupBy('airlines.name')
            ->orderByDesc('total')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pelanggaran',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => '#F59E0B', // Orange/Amber
                    'barThickness' => 20,
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    
    // Ubah jadi horizontal agar nama maskapai panjang tetap terbaca
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', 
        ];
    }
}