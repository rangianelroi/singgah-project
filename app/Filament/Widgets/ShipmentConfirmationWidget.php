<?php

namespace App\Filament\Widgets;

use App\Models\ConfiscatedItem;
use App\Models\ItemStatusLog;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Pagination\Paginator;
use Livewire\Attributes\On;

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
        return ConfiscatedItem::with(['passenger', 'latestStatusLog', 'communications', 'shipment.address'])
            ->inStorage()
            ->whereIn('pending_action', ['shipment_confirmation', 'payment_confirmation', 'payment_paid'])
            ->latest()
            ->paginate(5);
    }

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['team_leader_avsec']);
    }

    public function getWhatsAppUrl(ConfiscatedItem $item): string
    {
        if (empty($item->passenger->phone_number)) return '#';
        $passengerPhone = $item->passenger->phone_number;
        
        // Tahap 1: Shipment Confirmation - tidak ada form, hanya offer
        if ($item->pending_action === 'shipment_confirmation') {
            $message = "Selamat sore Bpk/Ibu {$item->passenger->full_name}, kami ingin menawarkan layanan pengiriman untuk barang Anda '{$item->item_name}'...";
            return "https://wa.me/{$passengerPhone}?text=" . urlencode($message);
        }
        
        // Tahap 2: Payment Confirmation - menunggu form harga diisi dulu
        if ($item->pending_action === 'payment_confirmation') {
            $shipment = $item->shipment;
            if (!$shipment?->shipping_cost) {
                // Belum ada data harga, disable link
                return '#';
            }
            $totalPrice = ($shipment?->shipping_cost ?? 0) + ($shipment?->service_fee ?? 0);
            $message = "Silakan lakukan transfer pembayaran sebesar Rp " . number_format($totalPrice, 0, ',', '.') 
                . " untuk pengiriman barang Anda '{$item->item_name}' ke " . $shipment?->address?->city;
            return "https://wa.me/{$passengerPhone}?text=" . urlencode($message);
        }
        
        // Tahap 3: Payment Paid - menunggu form resi diisi dulu
        if ($item->pending_action === 'payment_paid') {
            $shipment = $item->shipment;
            if (!$shipment?->tracking_number) {
                // Belum ada nomor resi, disable link
                return '#';
            }
            $message = "Barang Anda '{$item->item_name}' sudah dikirim. No. Resi: {$shipment->tracking_number}. Silakan lacak di kurir.";
            return "https://wa.me/{$passengerPhone}?text=" . urlencode($message);
        }
        
        return '#';
    }

    /**
     * Action: Konfirmasi Pengiriman (Shipment)
     */
    public function confirmShipmentAction(): Action
    {
        return Action::make('confirmShipmentAction')
            ->label('Catat')
            ->icon('heroicon-o-pencil-square')
            ->modalHeading('Konfirmasi Pengiriman')
            ->modalWidth('2xl')
            ->form([
                Select::make('shipment_response')
                    ->label('Respon Penumpang')
                    ->options([
                        'yes' => '✅ Setuju Pengiriman',
                        'no' => '❌ Tolak Pengiriman',
                    ])
                    ->required()
                    ->reactive()
                    ->columnSpanFull(),

                // Form untuk YES
                Grid::make(2)->schema([
                    TextInput::make('recipient_name')
                        ->label('Nama Penerima')
                        ->required(),
                    TextInput::make('recipient_phone')
                        ->label('No. Telepon')
                        ->tel()
                        ->required(),
                ])->visible(fn ($get) => $get('shipment_response') === 'yes'),

                Textarea::make('street_address')
                    ->label('Alamat Jalan')
                    ->required()
                    ->visible(fn ($get) => $get('shipment_response') === 'yes')
                    ->columnSpanFull(),

                Grid::make(4)->schema([
                    TextInput::make('subdistrict')->label('Kelurahan')->nullable(),
                    TextInput::make('district')->label('Kecamatan')->nullable(),
                    TextInput::make('city')->label('Kota')->required(),
                    TextInput::make('province')->label('Provinsi')->required(),
                    TextInput::make('postal_code')->label('Kode Pos')->required(),
                    TextInput::make('country')->label('Negara')->required()->default('Indonesia'),
                ])->visible(fn ($get) => $get('shipment_response') === 'yes'),

                // Form untuk NO
                Textarea::make('rejection_reason')
                    ->label('Alasan Penolakan')
                    ->visible(fn ($get) => $get('shipment_response') === 'no')
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, array $arguments) {
                $record = ConfiscatedItem::find($arguments['record'] ?? null);
                if (!$record) {
                    Notification::make()->title('Error')->body('Barang tidak ditemukan')->danger()->send();
                    return;
                }

                if ($data['shipment_response'] === 'yes') {
                    // Create or update address
                    $address = $record->passenger->addresses()->create([
                        'recipient_name' => $data['recipient_name'],
                        'recipient_phone' => $data['recipient_phone'],
                        'street_address' => $data['street_address'],
                        'subdistrict' => $data['subdistrict'] ?? null,
                        'district' => $data['district'] ?? null,
                        'city' => $data['city'],
                        'province' => $data['province'],
                        'postal_code' => $data['postal_code'],
                        'country' => $data['country'],
                    ]);

                    // Create or update shipment with ONLY address_id reference
                    // IMPORTANT: Address details are stored in Address model, not duplicated here
                    $shipment = $record->shipment()->updateOrCreate(
                        ['item_id' => $record->id],
                        [
                            'address_id' => $address->id,
                            'payment_status' => 'pending',
                        ]
                    );

                    // Update pending_action to payment_confirmation
                    $record->update(['pending_action' => 'payment_confirmation']);
                    
                    $record->communications()->create([
                        'user_id' => auth()->id(),
                        'channel' => 'other',
                        'communication_type' => 'shipment_inquiry',
                        'communication_status' => 'shipment_confirmed',
                        'message_summary' => "Setuju dikirim ke {$data['city']}",
                        'response_received' => true,
                        'responded_at' => now(),
                        'response_notes' => $data['recipient_name'] . " - " . $data['street_address'],
                        'sent_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Pengiriman Dikonfirmasi')
                        ->body('Menunggu konfirmasi pembayaran')
                        ->success()
                        ->send();
                } else {
                    $record->update(['pending_action' => null]);
                    
                    $record->communications()->create([
                        'user_id' => auth()->id(),
                        'channel' => 'other',
                        'communication_type' => 'shipment_inquiry',
                        'communication_status' => 'shipment_declined',
                        'message_summary' => 'Barang tidak ingin dikirim',
                        'response_received' => true,
                        'responded_at' => now(),
                        'response_notes' => $data['rejection_reason'] ?? '',
                        'sent_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Pengiriman Ditolak')
                        ->body('Barang kembali ke gudang')
                        ->warning()
                        ->send();
                }

                $this->dispatch('item-processed');
            });
    }

    /**
     * Action: Input Harga Pengiriman & Layanan
     */
    public function inputPriceAction(): Action
    {
        return Action::make('inputPriceAction')
            ->label('Isi Harga')
            ->icon('heroicon-o-pencil-square')
            ->modalHeading('Input Harga Pengiriman')
            ->modalWidth('md')
            ->form([
                Grid::make(2)->schema([
                    TextInput::make('shipping_cost')
                        ->label('Harga Pengiriman (Rp)')
                        ->numeric()
                        ->required()
                        ->inputMode('decimal'),
                    TextInput::make('service_fee')
                        ->label('Harga Layanan (Rp)')
                        ->numeric()
                        ->required()
                        ->inputMode('decimal'),
                ])->columnSpanFull(),
            ])
            ->action(function (array $data, array $arguments) {
                $record = ConfiscatedItem::find($arguments['record'] ?? null);
                if (!$record) {
                    Notification::make()->title('Error')->body('Barang tidak ditemukan')->danger()->send();
                    return;
                }

                $shipment = $record->shipment;
                if ($shipment) {
                    $shipment->update([
                        'shipping_cost' => $data['shipping_cost'],
                        'service_fee' => $data['service_fee'],
                    ]);
                }

                Notification::make()
                    ->title('Harga Tersimpan')
                    ->body('Silakan chat WA untuk memberitahu harga ke penumpang')
                    ->success()
                    ->send();
                    
                $this->dispatch('item-processed');
            });
    }

    /**
     * Action: Konfirmasi Pembayaran
     */
    public function confirmPaymentAction(): Action
    {
        return Action::make('confirmPaymentAction')
            ->label('Catat')
            ->icon('heroicon-o-pencil-square')
            ->modalHeading('Apakah Sudah Dibayar?')
            ->modalWidth('md')
            ->form([
                Select::make('payment_response')
                    ->label('Status Pembayaran')
                    ->options([
                        'yes' => '✅ Sudah Dibayar',
                        'no' => '❌ Belum Dibayar',
                    ])
                    ->required()
                    ->reactive()
                    ->columnSpanFull(),

                FileUpload::make('payment_proof_path')
                    ->label('Bukti Transfer (Foto/Screenshot)')
                    ->image()
                    ->disk('public')
                    ->directory('payment-proofs')
                    ->required()
                    ->visible(fn ($get) => $get('payment_response') === 'yes')
                    ->columnSpanFull(),

                Textarea::make('payment_fail_reason')
                    ->label('Keterangan')
                    ->visible(fn ($get) => $get('payment_response') === 'no')
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, array $arguments) {
                $record = ConfiscatedItem::find($arguments['record'] ?? null);
                if (!$record) {
                    Notification::make()->title('Error')->body('Barang tidak ditemukan')->danger()->send();
                    return;
                }

                $shipment = $record->shipment;

                if ($data['payment_response'] === 'yes') {
                    // Update shipment dengan bukti pembayaran
                    if ($shipment) {
                        $shipment->update([
                            'payment_proof_path' => $data['payment_proof_path'] ?? null,
                            'payment_status' => 'paid',
                        ]);
                    }

                    // Update pending_action ke payment_paid (menunggu resi)
                    $record->update(['pending_action' => 'payment_paid']);
                    
                    $record->communications()->create([
                        'user_id' => auth()->id(),
                        'channel' => 'other',
                        'communication_type' => 'payment_follow_up',
                        'communication_status' => 'payment_confirmed',
                        'message_summary' => 'Pembayaran dikonfirmasi. Menunggu pengiriman resi.',
                        'response_received' => true,
                        'responded_at' => now(),
                        'response_notes' => "Bukti: {$data['payment_proof_path']}",
                        'sent_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Pembayaran Dikonfirmasi')
                        ->body('Lanjut ke tahap pengiriman resi')
                        ->success()
                        ->send();
                } else {
                    // Tolak pembayaran - kembalikan ke gudang
                    $record->update(['pending_action' => null]);

                    if ($shipment) {
                        $shipment->update(['payment_status' => 'failed']);
                    }

                    $record->communications()->create([
                        'user_id' => auth()->id(),
                        'channel' => 'other',
                        'communication_type' => 'payment_follow_up',
                        'communication_status' => 'payment_failed',
                        'message_summary' => 'Pembayaran belum diterima',
                        'response_received' => true,
                        'responded_at' => now(),
                        'response_notes' => $data['payment_fail_reason'] ?? '',
                        'sent_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Pembayaran Belum Dikonfirmasi')
                        ->body('Barang kembali ke gudang')
                        ->warning()
                        ->send();
                }

                $this->dispatch('item-processed');
            });
    }

    /**
     * Action: Input Nomor Resi (setelah pembayaran diterima)
     */
    public function inputTrackingAction(): Action
    {
        return Action::make('inputTrackingAction')
            ->label('Isi Resi')
            ->icon('heroicon-o-pencil-square')
            ->modalHeading('Input Nomor Resi')
            ->modalWidth('md')
            ->form([
                TextInput::make('tracking_number')
                    ->label('Nomor Resi')
                    ->required()
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, array $arguments) {
                $record = ConfiscatedItem::find($arguments['record'] ?? null);
                if (!$record) {
                    Notification::make()->title('Error')->body('Barang tidak ditemukan')->danger()->send();
                    return;
                }

                $shipment = $record->shipment;
                if ($shipment) {
                    $shipment->update([
                        'tracking_number' => $data['tracking_number'],
                    ]);
                }

                Notification::make()
                    ->title('Nomor Resi Tersimpan')
                    ->body('Silakan chat WA untuk mengirimkan resi ke penumpang')
                    ->success()
                    ->send();
                    
                $this->dispatch('item-processed');
            });
    }

    /**
     * Action: Konfirmasi Pengiriman Resi
     */
    public function confirmTrackingSentAction(): Action
    {
        return Action::make('confirmTrackingSentAction')
            ->label('Catat')
            ->icon('heroicon-o-pencil-square')
            ->modalHeading('Apakah Resi Sudah Dikirim?')
            ->modalWidth('md')
            ->form([
                Select::make('tracking_sent')
                    ->label('Status Pengiriman Resi')
                    ->options([
                        'yes' => '✅ Sudah Dikirim',
                        'no' => '❌ Belum Dikirim',
                    ])
                    ->required()
                    ->reactive()
                    ->columnSpanFull(),

                Textarea::make('tracking_notes')
                    ->label('Catatan')
                    ->visible(fn ($get) => $get('tracking_sent') === 'no')
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, array $arguments) {
                $record = ConfiscatedItem::find($arguments['record'] ?? null);
                if (!$record) {
                    Notification::make()->title('Error')->body('Barang tidak ditemukan')->danger()->send();
                    return;
                }

                $shipment = $record->shipment;

                if ($data['tracking_sent'] === 'yes') {
                    // Update shipment tracking sent time
                    if ($shipment) {
                        $shipment->update([
                            'tracking_number_sent_at' => now(),
                        ]);
                    }

                    // Update item status to SHIPPED and clear pending_action
                    $record->update(['pending_action' => null]);
                    
                    ItemStatusLog::create([
                        'item_id' => $record->id,
                        'user_id' => auth()->id(),
                        'status' => 'SHIPPED',
                        'notes' => "Nomor resi sudah dikirim ke penumpang. Resi: {$shipment?->tracking_number}",
                    ]);

                    $record->communications()->create([
                        'user_id' => auth()->id(),
                        'channel' => 'other',
                        'communication_type' => 'shipment_inquiry',
                        'communication_status' => 'tracking_sent',
                        'message_summary' => "Nomor resi dikirim: {$shipment?->tracking_number}",
                        'response_received' => false,
                        'sent_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Barang Dikirim')
                        ->body('Barang pindah ke widget Shipped Items')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Belum Dikirim')
                        ->body('Silakan kirimkan resi ke penumpang terlebih dahulu')
                        ->warning()
                        ->send();
                }

                $this->dispatch('item-processed');
            });
    }
}
