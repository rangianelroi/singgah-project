<?php

namespace App\Filament\Widgets;

use App\Models\ConfiscatedItem;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
        // PERBAIKAN: Tambahkan with(['latestStatusLog']) agar data log bisa diakses di Blade
        $this->inStorageItems = ConfiscatedItem::with(['passenger', 'communications', 'latestStatusLog']) 
            ->whereHas('latestStatusLog', function ($query) {
                $query->where('status', 'IN_STORAGE');
            })->get();
    }

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['team_leader_avsec', 'admin']);
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

    public function manualLogAction(): Action
    {
        return Action::make('manualLogAction')
            ->label('Catat Log')
            ->icon('heroicon-o-pencil-square')
            ->modalHeading('Catat Log Komunikasi Manual')
            ->form([
                Select::make('channel')
                    ->label('Saluran Komunikasi')
                    ->options([
                        'whatsapp' => 'WhatsApp Manual',
                        'email' => 'Email',
                        'other' => 'Telepon / Tatap Muka / Lainnya',
                    ])
                    ->default('other')
                    ->required(),
                Textarea::make('message_summary')
                    ->label('Ringkasan / Catatan')
                    ->placeholder('Contoh: Ditelepon tidak diangkat, atau Penumpang berjanji akan mengambil barang lusa.')
                    ->required(),
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
                    'message_summary' => $data['message_summary'],
                    'sent_at' => now(),
                ]);

                Notification::make()->title('Log Berhasil Dicatat')->success()->send();
            });
    }

    public function startCommunicationAction(): Action
    {
        return Action::make('startCommunication')
            ->label('Mulai Komunikasi')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->color('info')
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-chat-bubble-left-right')
            ->modalHeading('Mulai Komunikasi dengan Penumpang')
            ->modalDescription('Aksi ini akan mengubah status barang menjadi "Menunggu Konfirmasi Pengiriman" dan akan tercatat di Log Komunikasi. Lanjutkan?')
            ->action(function (Action $action) {
                $record = ConfiscatedItem::find($action->getArguments()['record'] ?? null);
                if (!$record) {
                    Notification::make()->title('Gagal!')->body('Barang tidak ditemukan.')->danger()->send();
                    return;
                }

                $record->communications()->create([
                    'user_id' => auth()->id(),
                    'channel' => 'other', 
                    'message_summary' => 'Sistem: Memulai proses komunikasi dengan penumpang.',
                    'sent_at' => now(),
                ]);

                $record->statusLogs()->create([
                    'status' => 'PENDING_SHIPMENT_CONFIRMATION',
                    'user_id' => auth()->id(),
                    'notes' => 'Menunggu konfirmasi detail pengiriman dari penumpang.',
                ]);

                Notification::make()->title('Komunikasi Dimulai')->success()->send();
                $this->dispatch('item-processed');
            });
    }
}