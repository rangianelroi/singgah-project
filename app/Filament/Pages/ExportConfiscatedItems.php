<?php

namespace App\Filament\Pages;

use App\Models\ConfiscatedItem;
use Filament\Forms\Components\DatePicker; // Form Component
use Filament\Schemas\Components\Section;   // Layout Component (v4)
use Filament\Schemas\Schema;               // Schema Object (v4)
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use BackedEnum;

class ExportConfiscatedItems extends Page
{
    // Sesuaikan type hint navigationIcon (v4 support enum)
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-arrow-down';
    
    protected static ?string $navigationLabel = 'Export Laporan';
    protected static ?string $title = 'Export Data Barang Sitaan';
    protected static ?string $slug = 'export-laporan';
    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.export-confiscated-items';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return in_array(auth()->user()->role, ['department_head_avsec', 'admin', 'squad_leader_avsec']);
    }

    public function mount(): void
    {
        // Pada v4, inisialisasi form state biasanya otomatis handle statePath
        $this->form->fill();
    }

    /**
     * Definisi Schema (Pengganti Form di v3)
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([ // Root menggunakan components()
                Section::make('Filter Laporan')
                    ->description('Pilih rentang waktu data yang ingin diexport ke PDF.')
                    ->schema([ // Nesting di layout menggunakan schema()
                        DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->required()
                            ->afterOrEqual('start_date'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function exportPdf()
    {
        // Mengambil data state
        $data = $this->form->getState();

        $items = ConfiscatedItem::with(['passenger', 'flight'])
            ->whereDate('confiscation_date', '>=', $data['start_date'])
            ->whereDate('confiscation_date', '<=', $data['end_date'])
            ->orderBy('confiscation_date', 'desc')
            ->get();

        if ($items->isEmpty()) {
            Notification::make()
                ->title('Data Kosong')
                ->body('Tidak ada barang sitaan pada rentang tanggal tersebut.')
                ->warning()
                ->send();
            return;
        }

        $pdf = Pdf::loadView('reports.laporan-barang-sitaan', [
            'items' => $items,
            'startDate' => $data['start_date'],
            'endDate' => $data['end_date'],
        ]);

        $fileName = 'Laporan-Sitaan-' . now()->format('Ymd-His') . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }
}