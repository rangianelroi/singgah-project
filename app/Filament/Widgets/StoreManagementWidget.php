<?php

namespace App\Filament\Widgets;

use App\Models\ConfiscatedItem;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;

class StorageManagementWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected string $view = 'filament.widgets.storage-management-widget';
    public ?Collection $itemsForStorage;

    #[On('item-processed')] 
    public function mount(): void
    {
        // Ambil item yang status terakhirnya 'VERIFIED_FOR_STORAGE'
        $this->itemsForStorage = ConfiscatedItem::whereHas('statusLogs', function ($query) {
            $query->where('status', 'VERIFIED_FOR_STORAGE')
                ->whereRaw('id = (select max(id) from item_status_logs where item_id = confiscated_items.id)');
        })->get();
    }

    /**
     * Widget ini hanya bisa dilihat oleh Team Leader dan Admin
     */
    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['team_leader_avsec', 'admin']);
    }

    /**
     * Aksi untuk menyimpan barang ke gudang
     */
    public function storeItemAction(): Action
    {
        return Action::make('storeItem')
            ->label('Proses ke Gudang')
            ->icon('heroicon-o-archive-box-arrow-down')
            ->color('primary')
            ->form([
                TextInput::make('storage_location')
                    ->label('Lokasi Penyimpanan di Gudang')
                    ->placeholder('Contoh: Rak A1, Boks 05')
                    ->required(),
            ])
            // ===================================================
            // PERBAIKAN DI SINI: Menggunakan pola yang lebih stabil
            // ===================================================
            ->action(function (array $data, Action $action) {
                // Ambil record secara manual dari argumen aksi
                $record = ConfiscatedItem::find($action->getArguments()['record'] ?? null);

                if (!$record) {
                    Notification::make()->title('Gagal!')->body('Barang tidak ditemukan.')->danger()->send();
                    return;
                }

                // Update lokasi penyimpanan di tabel utama
                $record->update([
                    'storage_location' => $data['storage_location'],
                ]);

                // Buat log status baru
                $record->statusLogs()->create([
                    'status' => 'IN_STORAGE',
                    'user_id' => auth()->id(),
                    'notes' => 'Barang telah disimpan di gudang. Lokasi: ' . $data['storage_location'],
                ]);

                Notification::make()->title('Barang berhasil disimpan!')->success()->send();
                
                // Muat ulang data untuk kedua widget
                $this->dispatch('item-processed');
            });
    }
}