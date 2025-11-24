<?php

namespace App\Filament\Widgets;

use App\Models\ConfiscatedItem;
use App\Models\ItemStatusLog;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Widgets\Widget;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class PendingPickupWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms, WithPagination;

    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    
    protected string $view = 'filament.widgets.pending-pickup-widget';

    protected function getItems()
    {
        // PERBAIKAN: Filter berdasarkan relasi latestStatusLog, bukan kolom status
        return ConfiscatedItem::with(['passenger', 'flight', 'pickups'])
            ->whereHas('latestStatusLog', function ($query) {
                $query->where('status', 'PENDING_PICKUP');
            })
            ->latest()
            ->paginate(6);
    }

    public function confirmHandoverAction(): Action
    {
        return Action::make('confirmHandoverAction')
            ->label('Konfirmasi Serah Terima')
            ->icon('heroicon-o-hand-raised')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Serah Terima Barang')
            ->modalDescription('Pastikan identitas penjemput sesuai dengan data yang telah diverifikasi sebelumnya.')
            ->form([
                FileUpload::make('handover_photo_path')
                    ->label('Foto Dokumentasi Serah Terima')
                    ->helperText('Foto petugas menyerahkan barang ke penjemput')
                    ->image()
                    ->disk('public')
                    ->directory('handover-photos')
                    ->required(),
                
                Textarea::make('handover_notes')
                    ->label('Catatan Tambahan')
                    ->placeholder('Contoh: Barang diterima dalam kondisi baik.')
                    ->rows(2),
            ])
            ->action(function (array $data, array $arguments) {
                $recordId = $arguments['record'] ?? null;
                $record = ConfiscatedItem::find($recordId);

                if (!$record) {
                    Notification::make()->title('Error')->body('Data tidak ditemukan')->danger()->send();
                    return;
                }

                // PERBAIKAN: Hapus update status kolom utama yang tidak ada
                // $record->update(['status' => 'PICKED_UP']); <-- INI PENYEBAB ERROR SEBELUMNYA

                // Cukup buat Log Baru. Sistem akan membaca ini sebagai status terkini.
                ItemStatusLog::create([
                    'item_id' => $record->id,
                    'user_id' => Auth::id(),
                    'status'  => 'PICKED_UP',
                    'notes'   => 'Barang telah diserahterimakan. ' . ($data['handover_notes'] ?? ''),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Notification::make()
                    ->title('Serah Terima Berhasil')
                    ->body('Status barang diperbarui menjadi Picked Up.')
                    ->success()
                    ->send();
            });
    }

    // ... kode approveAction dan lainnya di atas ...

    public static function canView(): bool
    {
        // Hanya tampil jika user adalah Squad Leader
        return auth()->user()->role === 'squad_leader_avsec';
    }
}