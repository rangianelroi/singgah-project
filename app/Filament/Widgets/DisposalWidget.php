<?php

namespace App\Filament\Widgets;

use App\Models\ConfiscatedItem;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class DisposalWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected string $view = 'filament.widgets.disposal-widget';

    protected int | string | array $columnSpan = 'full';
    
    public ?Collection $itemsForDisposal;
    
    // [BARU] Array untuk menyimpan ID item yang dicentang
    public array $selectedItems = [];

    public static function canView(): bool
    {
        // Pastikan user role sesuai dengan sistem Anda
        return in_array(auth()->user()->role, ['department_head_avsec']);
    }

    #[On('item-processed')]
    public function mount(): void
    {
        // Query tetap sama seperti sebelumnya
        $this->itemsForDisposal = ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
            $query->where('status', 'IN_STORAGE');
        })
        ->where('confiscation_date', '<=', now()->subMinute()) 
        ->get();
    }

    // [BARU] Helper untuk Select All / Deselect All
    public function toggleSelectAll()
    {
        if (count($this->selectedItems) === $this->itemsForDisposal->count()) {
            $this->selectedItems = [];
        } else {
            $this->selectedItems = $this->itemsForDisposal->pluck('id')->map(fn($id) => (string) $id)->toArray();
        }
    }

    /**
     * Action Tunggal (Existing)
     */
    public function processDisposalAction(): Action
    {
        return Action::make('processDisposal')
            ->label('Proses')
            ->icon('heroicon-o-fire')
            ->color('danger')
            ->form($this->getDisposalFormSchema()) // Refactor schema ke function terpisah
            ->action(function (array $data, Action $action) {
                $record = ConfiscatedItem::find($action->getArguments()['record'] ?? null);
                if (!$record) return;
                
                $this->executeDisposal($record, $data); // Refactor eksekusi ke function

                Notification::make()->title('Selesai')->body('Barang berhasil diproses.')->success()->send();
                $this->dispatch('item-processed');
            });
    }

    /**
     * [BARU] Action Massal (Bulk)
     */
    public function processBulkDisposalAction(): Action
    {
        return Action::make('processBulkDisposal')
            ->label('Proses Terpilih (' . count($this->selectedItems) . ')')
            ->icon('heroicon-o-check-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Pemusnahan Massal')
            ->modalDescription('Apakah Anda yakin ingin memproses semua barang yang dipilih dengan metode yang sama?')
            ->form($this->getDisposalFormSchema())
            ->action(function (array $data) {
                if (empty($this->selectedItems)) {
                    Notification::make()->title('Gagal')->body('Tidak ada barang yang dipilih.')->warning()->send();
                    return;
                }

                // Ambil semua record sekaligus dengan single query
                $records = ConfiscatedItem::whereIn('id', $this->selectedItems)->get();
                
                if ($records->isEmpty()) {
                    Notification::make()->title('Gagal')->body('Barang tidak ditemukan.')->danger()->send();
                    return;
                }

                // Prepare disposal records untuk bulk insert
                $disposalRecords = [];
                $statusLogRecords = [];
                $now = now();
                $userId = auth()->id();

                foreach ($records as $record) {
                    $disposalRecords[] = [
                        'item_id' => $record->id,
                        'disposal_method' => $data['disposal_method'],
                        'disposal_date' => $data['disposal_date'],
                        'witnesses' => $data['witnesses'] ?? null,
                        'authorized_by_user_id' => $userId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $statusLogRecords[] = [
                        'item_id' => $record->id,
                        'status' => 'DISPOSED',
                        'user_id' => $userId,
                        'notes' => 'Bulk process via Dashboard Dept Head. Metode: ' . $data['disposal_method'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Bulk insert dengan chunk untuk menghindari query terlalu besar
                collect($disposalRecords)->chunk(100)->each(function ($chunk) {
                    \DB::table('disposal_records')->insert($chunk->toArray());
                });

                collect($statusLogRecords)->chunk(100)->each(function ($chunk) {
                    \DB::table('item_status_logs')->insert($chunk->toArray());
                });

                // Reset pilihan setelah sukses
                $this->selectedItems = [];
                
                $count = $records->count();
                Notification::make()
                    ->title('Sukses Massal')
                    ->body("$count barang berhasil diproses sekaligus.")
                    ->success()
                    ->send();
                
                $this->dispatch('item-processed');
            });
    }

    // Helper Schema Form (agar tidak duplikat kode)
    protected function getDisposalFormSchema(): array
    {
        return [
            Select::make('disposal_method')
                ->options([
                    'destroyed' => 'Dimusnahkan',
                    'handed_to_police' => 'Diserahkan ke Polisi',
                    'other' => 'Lainnya',
                ])
                ->required()->label('Metode Pemusnahan'),
            DatePicker::make('disposal_date')
                ->required()->default(now())->label('Tanggal Eksekusi'),
            Textarea::make('witnesses')
                ->label('Saksi')
                ->placeholder('Contoh: Budi, Anton'),
        ];
    }

    // Helper Eksekusi Database
    protected function executeDisposal($record, $data)
    {
        // Gunakan DB insert langsung untuk lebih cepat
        $now = now();
        $userId = auth()->id();

        DB::table('disposal_records')->insert([
            'item_id' => $record->id,
            'disposal_method' => $data['disposal_method'],
            'disposal_date' => $data['disposal_date'],
            'witnesses' => $data['witnesses'] ?? null,
            'authorized_by_user_id' => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('item_status_logs')->insert([
            'item_id' => $record->id,
            'status' => 'DISPOSED',
            'user_id' => $userId,
            'notes' => 'Single process via Dashboard Dept Head. Metode: ' . $data['disposal_method'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}