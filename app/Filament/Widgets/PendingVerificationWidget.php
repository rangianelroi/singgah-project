<?php
namespace App\Filament\Widgets;

use App\Models\ConfiscatedItem;
use Filament\Actions\Action;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Filament\Actions\Concerns\InteractsWithActions; // <-- Tambahkan ini
use Filament\Actions\Contracts\HasActions; // <-- Tambahkan ini
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;    

class PendingVerificationWidget extends Widget implements HasActions, HasForms // <-- Tambahkan 'implements HasActions'
{
    use InteractsWithActions, InteractsWithForms; // <-- Tambahkan ini

    protected string $view = 'filament.widgets.pending-verification-widget';
    public ?Collection $pendingItems;

    public function mount(): void
    {
            $this->pendingItems = ConfiscatedItem::whereHas('statusLogs', function ($query) {
                $query->where('status', 'RECORDED')
                    ->whereRaw('id = (select max(id) from item_status_logs where item_id = confiscated_items.id)');
            })->get();
    }
    
    public static function canView(): bool
    {
        return auth()->user()->role === 'squad_leader_avsec';
    }
    
    // --- Pindahkan logic Aksi ke sini ---
    public function approveAction(): Action
    {
        return Action::make('approve')
            ->label('Setujui')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->form([
                Wizard::make([
                    Step::make('Konfirmasi Pengambilan')
                        ->schema([
                            Toggle::make('is_picked_up_by_relative')
                                ->label('Apakah barang ini akan diambil oleh kerabat dalam 1x24 jam?')
                                ->onIcon('heroicon-o-check')
                                ->offIcon('heroicon-o-x-mark')
                                ->live(), 
                        ]),
                    
                    Step::make('Data Kerabat')
                        ->schema([
                            TextInput::make('pickup_by_name')
                                ->label('Nama Lengkap Pengambil')
                                ->required(),
                            TextInput::make('pickup_by_identity_number')
                                ->label('No. Identitas (KTP/Paspor)')
                                ->required(),
                            TextInput::make('relationship_to_passenger')
                                ->label('Hubungan dengan Penumpang')
                                ->required(),
                            FileUpload::make('photo_of_recipient_path')
                                ->label('Foto Pengambil (Selfie dengan Identitas)')
                                ->image()
                                ->disk('local')
                                ->directory('pickup-photos')
                                ->required(),
                            FileUpload::make('photo_of_identity_path')
                                ->label('Foto Identitas (KTP/Paspor)')
                                ->image()
                                ->disk('local')
                                ->directory('identity-photos')
                                ->required(),
                        ])
                        ->visible(fn (Get $get) => $get('../is_picked_up_by_relative')),
                ])->contained(false)
            ])
            ->action(function (array $data, Action $action) {
                // 1. Ambil argumen dari saat aksi di-mount
                $arguments = $action->getArguments();

                // 2. Cari record secara manual
                $record = ConfiscatedItem::find($arguments['record'] ?? null);

                // 3. Jika record tidak ada, hentikan dan beri notifikasi
                if (!$record) {
                    Notification::make()->title('Gagal memproses!')->body('Barang tidak ditemukan.')->danger()->send();
                    return;
                }
                // Logika ini sama persis seperti yang ada di tabel
                if ($data['is_picked_up_by_relative']) {
                    $record->pickups()->create([
                        'pickup_by_name' => $data['pickup_by_name'],
                        'pickup_by_identity_number' => $data['pickup_by_identity_number'],
                        'relationship_to_passenger' => $data['relationship_to_passenger'],
                        'photo_of_recipient_path' => $data['photo_of_recipient_path'],
                        'photo_of_identity_path' => $data['photo_of_identity_path'],
                        'verified_by_user_id' => auth()->id(),
                        'pickup_timestamp' => now(),
                    ]);

                    $record->statusLogs()->create([
                        'status' => 'PENDING_PICKUP',
                        'user_id' => auth()->id(),
                        'notes' => 'Menunggu pengambilan oleh kerabat: ' . $data['pickup_by_name'],
                    ]);
                } else {
                    $record->statusLogs()->create([
                        'status' => 'VERIFIED_FOR_STORAGE',
                        'user_id' => auth()->id(),
                        'notes' => 'Barang diverifikasi untuk disimpan di gudang.',
                    ]);
                }
                
                // Refresh widget data
                $this->mount();
            });
    }
}