<?php

namespace App\Filament\Widgets;

use App\Models\ConfiscatedItem;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;

class InStorageItemsWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected string $view = 'filament.widgets.in-storage-items-widget';
    public ?Collection $inStorageItems;

    #[On('item-processed')]
    public function mount(): void
    {
        $this->inStorageItems = ConfiscatedItem::with(['passenger', 'communications', 'latestStatusLog'])
            ->inStorage()
            ->whereNull('pending_action') // Hanya barang yang belum ada komunikasi
            ->get();
    }

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['team_leader_avsec']);
    }

    public function getWhatsAppUrl(ConfiscatedItem $item): string
    {
        if (empty($item->passenger->phone_number)) {
            return '#';
        }
        $passengerPhone = $item->passenger->phone_number;
        $message = "Selamat sore Bpk/Ibu {$item->passenger->full_name}, kami dari AVSEC Bandara Sam Ratulangi ingin menginformasikan mengenai barang Anda '{$item->item_name}'...";
        return "https://wa.me/{$passengerPhone}?text=" . urlencode($message);
    }

    /**
     * Proses Pengiriman - Set pending_action ke shipment_confirmation
     */
    public function processShipmentAction(): Action
    {
        return Action::make('processShipmentAction')
            ->label('Proses')
            ->icon('heroicon-o-arrow-right')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Mulai Proses Pengiriman')
            ->modalDescription('Barang akan dipindahkan ke tahap konfirmasi pengiriman. Lanjutkan?')
            ->action(function (array $arguments) {
                $record = ConfiscatedItem::find($arguments['record'] ?? null);
                if (!$record) {
                    Notification::make()->title('Gagal!')->body('Barang tidak ditemukan.')->danger()->send();
                    return;
                }

                // Update pending_action
                $record->update(['pending_action' => 'shipment_confirmation']);

                // Create communication log
                $record->communications()->create([
                    'user_id' => auth()->id(),
                    'channel' => 'other',
                    'communication_type' => 'initial_contact',
                    'communication_status' => 'sent',
                    'message_summary' => 'Sistem: Memulai proses komunikasi pengiriman dengan penumpang.',
                    'response_received' => false,
                    'sent_at' => now(),
                ]);

                Notification::make()
                    ->title('Proses Dimulai')
                    ->body('Barang siap untuk komunikasi pengiriman')
                    ->success()
                    ->send();
                    
                $this->dispatch('item-processed');
            });
    }

    /**
     * Enhanced Log Action dengan Status Tracking
     */
    public function logCommunicationAction(): Action
    {
        return Action::make('logCommunicationAction')
            ->label('Catat Komunikasi')
            ->icon('heroicon-o-pencil-square')
            ->modalHeading('Catat Log Komunikasi')
            ->modalWidth('2xl')
            ->form([
                Select::make('communication_type')
                    ->label('Jenis Komunikasi')
                    ->options([
                        'general' => '💬 Umum',
                        'initial_contact' => '👋 Kontak Awal',
                        'shipment_inquiry' => '🚚 Tanya Pengiriman',
                        'payment_follow_up' => '💰 Follow Up Pembayaran',
                        'confirmation' => '✅ Konfirmasi',
                    ])
                    ->default('initial_contact')
                    ->required()
                    ->reactive()
                    ->columnSpanFull(),

                Select::make('channel')
                    ->label('Saluran Komunikasi')
                    ->options([
                        'whatsapp' => '💬 WhatsApp',
                        'email' => '📧 Email',
                        'phone_call' => '📞 Telepon',
                        'other' => '📝 Lainnya',
                    ])
                    ->default('whatsapp')
                    ->required(),

                Select::make('communication_status')
                    ->label('Status Komunikasi')
                    ->options([
                        'sent' => '📤 Terkirim (belum direspon)',
                        'responded' => '✅ Direspon',
                        'shipment_confirmed' => '🚚 Setuju Kirim',
                        'shipment_declined' => '❌ Tolak Kirim',
                        'waiting_response' => '⏳ Menunggu Respon',
                    ])
                    ->default('sent')
                    ->required()
                    ->reactive(),

                Textarea::make('message_summary')
                    ->label('Ringkasan Komunikasi')
                    ->placeholder('Contoh: Penumpang ditanya apakah mau barang dikirim atau diambil.')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('response_received')
                    ->label('Sudah Ada Respon?')
                    ->default(false)
                    ->reactive()
                    ->columnSpanFull()
                    ->visible(fn ($get) => in_array($get('communication_status'), ['responded', 'shipment_confirmed', 'shipment_declined'])),

                DateTimePicker::make('responded_at')
                    ->label('Waktu Respon')
                    ->default(now())
                    ->visible(fn ($get) => $get('response_received'))
                    ->columnSpanFull(),

                Textarea::make('response_notes')
                    ->label('Catatan Respon')
                    ->placeholder('Contoh: Penumpang setuju dikirim, akan transfer besok.')
                    ->rows(3)
                    ->visible(fn ($get) => $get('response_received'))
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, array $arguments) {
                $record = ConfiscatedItem::find($arguments['record'] ?? null);
                if (!$record) {
                    Notification::make()->title('Error')->body('Barang tidak ditemukan')->danger()->send();
                    return;
                }

                $record->communications()->create([
                    'user_id' => auth()->id(),
                    'channel' => $data['channel'],
                    'communication_type' => $data['communication_type'],
                    'communication_status' => $data['communication_status'],
                    'message_summary' => $data['message_summary'],
                    'response_received' => $data['response_received'] ?? false,
                    'responded_at' => $data['responded_at'] ?? null,
                    'response_notes' => $data['response_notes'] ?? null,
                    'sent_at' => now(),
                ]);

                Notification::make()
                    ->title('Log Komunikasi Berhasil Dicatat')
                    ->success()
                    ->send();
            });
    }

    /**
     * Enhanced Start Communication dengan Auto-Log
     */
    public function startCommunicationAction(): Action
    {
        return Action::make('startCommunicationAction')
            ->label('Mulai Komunikasi')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->color('info')
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-chat-bubble-left-right')
            ->modalHeading('Mulai Proses Komunikasi Pengiriman')
            ->modalDescription('Barang akan masuk tahap "Menunggu Konfirmasi Pengiriman" dan akan tercatat di Log Komunikasi. Lanjutkan?')
            ->action(function (Action $action) {
                $record = ConfiscatedItem::find($action->getArguments()['record'] ?? null);
                if (!$record) {
                    Notification::make()->title('Gagal!')->body('Barang tidak ditemukan.')->danger()->send();
                    return;
                }

                // Create communication log
                $record->communications()->create([
                    'user_id' => auth()->id(),
                    'channel' => 'other',
                    'communication_type' => 'initial_contact',
                    'communication_status' => 'sent',
                    'message_summary' => 'Sistem: Memulai proses komunikasi pengiriman dengan penumpang.',
                    'response_received' => false,
                    'sent_at' => now(),
                ]);

                // Update status
                $record->statusLogs()->create([
                    'status' => 'PENDING_SHIPMENT_CONFIRMATION',
                    'user_id' => auth()->id(),
                    'notes' => 'Menunggu konfirmasi detail pengiriman dari penumpang.',
                ]);

                Notification::make()
                    ->title('Komunikasi Dimulai')
                    ->body('Log komunikasi otomatis tercatat')
                    ->success()
                    ->send();
                    
                $this->dispatch('item-processed');
            });
    }
}