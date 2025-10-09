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
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;

class ShipmentConfirmationWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected string $view = 'filament.widgets.shipment-confirmation-widget';
    public ?Collection $itemsForConfirmation;

    public $recipient_name, $recipient_phone, $street_address, $subdistrict, $district, $city, $province, $postal_code, $country, $shipping_cost, $tracking_number;

    #[On('item-processed')]
    public function mount(): void
    {
        $this->itemsForConfirmation = ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
            $query->where('status', 'PENDING_SHIPMENT_CONFIRMATION');
        })->get();
    }

    public function resetForm(): void
    {
        $this->recipient_name = ''; $this->recipient_phone = ''; $this->street_address = '';
        $this->subdistrict = ''; $this->district = ''; $this->city = '';
        $this->province = ''; $this->postal_code = ''; $this->country = 'Indonesia';
        $this->shipping_cost = 0; $this->tracking_number = '';
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

    // --- AKSI YANG SEBELUMNYA HILANG, SEKARANG SUDAH LENGKAP ---

    public function logResponseAction(): Action
    {
        return Action::make('logResponse')
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
            ->action(function (array $data, Action $action) {
                $record = ConfiscatedItem::find($action->getArguments()['record'] ?? null);
                if (!$record) return;

                $record->communications()->create([
                    'user_id' => auth()->id(),
                    'channel' => 'whatsapp', // Asumsi respon dari WA
                    'message_summary' => $data['message_summary'],
                    'sent_at' => now(),
                ]);

                Notification::make()->title('Respon berhasil dicatat!')->success()->send();
                $this->dispatch('item-processed');
            });
    }

    public function cancelShipmentProcessAction(): Action
    {
        return Action::make('cancelShipmentProcess')
            ->label('Batalkan')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription('Anda yakin ingin membatalkan proses pengiriman untuk barang ini? Status akan dikembalikan ke "IN_STORAGE".')
            ->action(function (Action $action) {
                $record = ConfiscatedItem::find($action->getArguments()['record'] ?? null);
                if (!$record) return;

                $record->statusLogs()->create([
                    'status' => 'IN_STORAGE',
                    'user_id' => auth()->id(),
                    'notes' => 'Proses pengiriman dibatalkan oleh petugas.',
                ]);
                
                Notification::make()->title('Proses Dibatalkan')->body('Barang dikembalikan ke status "Di Gudang".')->warning()->send();
                $this->dispatch('item-processed');
            });
    }
    
    // Aksi Konfirmasi & Kirim (tidak berubah)
    public function confirmShipmentAction(): Action
    {
        return Action::make('confirmShipment')
            ->label('Konfirmasi & Kirim')
            ->icon('heroicon-o-truck')
            ->color('success')
            ->modalSubmitActionLabel('Simpan Pengiriman')
            ->before(function (Action $action) { $this->resetForm(); if ($record = ConfiscatedItem::find($action->getArguments()['record'] ?? null)) { $this->recipient_name = $record->passenger->full_name; $this->recipient_phone = $record->passenger->phone_number; } })
            ->form([
                TextInput::make('recipient_name')->required()->label('Nama Penerima'),
                TextInput::make('recipient_phone')->tel()->required()->label('No. Telepon Penerima'),
                Textarea::make('street_address')->required()->label('Alamat Jalan')->columnSpanFull(),
                TextInput::make('subdistrict')->required()->label('Kelurahan/Desa'),
                TextInput::make('district')->required()->label('Kecamatan'),
                TextInput::make('city')->required()->label('Kota/Kabupaten'),
                TextInput::make('province')->required()->label('Provinsi'),
                TextInput::make('postal_code')->required()->label('Kode Pos'),
                TextInput::make('country')->required()->label('Negara'),
                TextInput::make('shipping_cost')->numeric()->prefix('Rp')->label('Ongkos Kirim')->required(),
                TextInput::make('tracking_number')->label('Nomor Resi (Opsional)'),
            ])
            ->action(function (Action $action) {
                $record = ConfiscatedItem::find($action->getArguments()['record'] ?? null); if (!$record) return;
                $data = [
                    'recipient_name' => $this->recipient_name, 'recipient_phone' => $this->recipient_phone,
                    'street_address' => $this->street_address, 'subdistrict' => $this->subdistrict,
                    'district' => $this->district, 'city' => $this->city, 'province' => $this->province,
                    'postal_code' => $this->postal_code, 'country' => $this->country,
                    'shipping_cost' => $this->shipping_cost, 'tracking_number' => $this->tracking_number,
                ];
                $record->shipment()->create($data);
                $record->statusLogs()->create([ 'status' => 'SHIPPED', 'user_id' => auth()->id(), 'notes' => 'Barang telah dikirim dengan No. Resi: ' . ($data['tracking_number'] ?? 'N/A'), ]);
                Notification::make()->title('Pengiriman Disimpan!')->success()->send();
                $this->dispatch('item-processed');
            });
    }
}