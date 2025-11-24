<?php

namespace App\Filament\Widgets;

use App\Models\ConfiscatedItem;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Pagination\Paginator;
use Livewire\Attributes\On;
use Filament\Schemas\Components\Grid;

class ShipmentConfirmationWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected string $view = 'filament.widgets.shipment-confirmation-widget';
    protected int | string | array $columnSpan = 'full';

    #[On('item-processed')]
    public function mount(): void
    {
    }

    public function getItems(): Paginator
    {
        return ConfiscatedItem::with(['passenger', 'latestStatusLog'])
            ->whereHas('latestStatusLog', function ($query) {
                $query->where('status', 'PENDING_SHIPMENT_CONFIRMATION');
            })
            ->latest()
            ->paginate(5);
    }

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['team_leader_avsec', 'admin']);
    }

    public function getWhatsAppUrl(ConfiscatedItem $item): string
    {
        if (empty($item->passenger->phone_number)) return '#';
        $passengerPhone = $item->passenger->phone_number;
        $message = "Selamat sore Bpk/Ibu {$item->passenger->full_name}, mohon konfirmasi alamat pengiriman dan biaya pengiriman untuk barang Anda '{$item->item_name}'...";
        return "https://wa.me/{$passengerPhone}?text=" . urlencode($message);
    }

    // --- AKSI: CATAT RESPON ---
    public function logResponseAction(): Action
    {
        return Action::make('logResponseAction') // Samakan nama ini dengan di Blade
            ->label('Catat Respon')
            ->icon('heroicon-o-pencil-square')
            ->color('gray')
            ->modalHeading('Catat Respon dari Penumpang')
            ->form([
                Textarea::make('message_summary')
                    ->label('Isi Respon atau Catatan')
                    ->required()
                    ->placeholder('Contoh: Penumpang setuju, alamat akan dikirim nanti.'),
            ])
            ->action(function (array $data, array $arguments) {
                $record = ConfiscatedItem::find($arguments['record'] ?? null);
                if (!$record) return;

                $record->communications()->create([
                    'user_id' => auth()->id(),
                    'channel' => 'whatsapp',
                    'message_summary' => $data['message_summary'],
                    'sent_at' => now(),
                ]);

                Notification::make()->title('Respon berhasil dicatat!')->success()->send();
            });
    }

    // --- AKSI: BATALKAN ---
    public function cancelShipmentProcessAction(): Action
    {
        return Action::make('cancelShipmentProcessAction')
            ->label('Batalkan')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription('Status akan dikembalikan ke "IN_STORAGE".')
            ->action(function (array $arguments) {
                $record = ConfiscatedItem::find($arguments['record'] ?? null);
                if (!$record) return;

                $record->statusLogs()->create([
                    'status' => 'IN_STORAGE',
                    'user_id' => auth()->id(),
                    'notes' => 'Proses pengiriman dibatalkan oleh petugas.',
                ]);
                
                Notification::make()->title('Proses Dibatalkan')->body('Status kembali ke Gudang.')->warning()->send();
            });
    }
    
    // --- AKSI: KONFIRMASI & KIRIM (FIXED) ---
    public function confirmShipmentAction(): Action
    {
        return Action::make('confirmShipmentAction')
            ->label('Konfirmasi & Kirim')
            ->icon('heroicon-o-truck')
            ->color('success')
            ->modalWidth('2xl')
            ->modalSubmitActionLabel('Simpan Pengiriman')
            // Mengisi form awal dengan data penumpang
            ->fillForm(function (array $arguments) {
                $record = ConfiscatedItem::find($arguments['record'] ?? null);
                return [
                    'recipient_name' => $record?->passenger->full_name,
                    'recipient_phone' => $record?->passenger->phone_number,
                    'country' => 'Indonesia',
                ];
            })
            ->form([
                TextInput::make('recipient_name')->required()->label('Nama Penerima'),
                TextInput::make('recipient_phone')->tel()->required()->label('No. Telepon Penerima'),
                Textarea::make('street_address')->required()->label('Alamat Jalan')->columnSpanFull(),
                
                // Grid layout agar lebih rapi
                    Grid::make(2)->schema([
                    TextInput::make('subdistrict')->required()->label('Kelurahan/Desa'),
                    TextInput::make('district')->required()->label('Kecamatan'),
                    TextInput::make('city')->required()->label('Kota/Kabupaten'),
                    TextInput::make('province')->required()->label('Provinsi'),
                    TextInput::make('postal_code')->required()->label('Kode Pos'),
                    TextInput::make('country')->required()->label('Negara')->default('Indonesia'),
                ]),

                TextInput::make('shipping_cost')->numeric()->prefix('Rp')->label('Ongkos Kirim')->required(),
                TextInput::make('tracking_number')->label('Nomor Resi (Opsional)'),
            ])
            ->action(function (array $data, array $arguments) {
                $record = ConfiscatedItem::find($arguments['record'] ?? null);
                if (!$record) return;

                // Simpan ke tabel shipment
                $record->shipment()->create([
                    'recipient_name' => $data['recipient_name'],
                    'recipient_phone' => $data['recipient_phone'],
                    'street_address' => $data['street_address'],
                    'subdistrict' => $data['subdistrict'],
                    'district' => $data['district'],
                    'city' => $data['city'],
                    'province' => $data['province'],
                    'postal_code' => $data['postal_code'],
                    'country' => $data['country'],
                    'shipping_cost' => $data['shipping_cost'],
                    'tracking_number' => $data['tracking_number'],
                ]);

                // Update status log
                $record->statusLogs()->create([
                    'status' => 'SHIPPED',
                    'user_id' => auth()->id(),
                    'notes' => 'Barang dikirim. Resi: ' . ($data['tracking_number'] ?? '-'),
                ]);

                Notification::make()->title('Pengiriman Disimpan!')->success()->send();
            });
    }
}