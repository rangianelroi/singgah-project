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
use Illuminate\Contracts\Pagination\Paginator; // <-- Import Paginator
use Livewire\Attributes\On;

class StorageManagementWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected string $view = 'filament.widgets.storage-management-widget';

    // Properti $itemsForStorage dan use WithPagination dihapus karena tidak perlu

    #[On('item-processed')]
    public function mount(): void
    {
        // Metode mount tetap ada untuk event listener, tapi tidak perlu mengisi properti
    }

    /**
     * Metode untuk mengambil data dengan paginasi.
     */
    public function getItems(): Paginator
    {
        return ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
                $query->where('status', 'VERIFIED_FOR_STORAGE');
            })
            ->latest()
            ->paginate(6); // Atur jumlah item per halaman
    }

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['team_leader_avsec', 'admin']);
    }

    /**
     * Aksi untuk menyimpan barang ke gudang
     */
    public function storeItem(): Action
    {
        return Action::make('storeItem')
            ->label('Proses ke Gudang')
            ->icon('heroicon-m-archive-box-arrow-down') // Menggunakan 'm' (mini) untuk konsistensi
            ->color('primary')
            ->size('sm') // Menambahkan size small
            ->form([
                TextInput::make('storage_location')
                    ->label('Lokasi Penyimpanan di Gudang')
                    ->placeholder('Contoh: Rak A1, Boks 05')
                    ->required(),
            ])
            ->action(function (array $data, Action $action) {
                $record = ConfiscatedItem::find($action->getArguments()['record'] ?? null);

                if (!$record) {
                    Notification::make()->title('Gagal!')->body('Barang tidak ditemukan.')->danger()->send();
                    return;
                }

                $record->update(['storage_location' => $data['storage_location']]);

                $record->statusLogs()->create([
                    'status' => 'IN_STORAGE',
                    'user_id' => auth()->id(),
                    'notes' => 'Barang telah disimpan di gudang. Lokasi: ' . $data['storage_location'],
                ]);

                Notification::make()->title('Barang berhasil disimpan!')->success()->send();
                $this->dispatch('item-processed');
            });
    }
}