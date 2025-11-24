<?php

namespace App\Filament\Widgets;

use App\Models\ConfiscatedItem;
use App\Models\ItemStatusLog;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Widgets\Widget;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class PendingVerificationWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms, WithPagination;

    protected string $view = 'filament.widgets.pending-verification-widget';
    protected int | string | array $columnSpan = 'full';

    protected function getItems()
    {
        // Ambil barang yang status terakhirnya 'RECORDED'
        return ConfiscatedItem::with('passenger')
            ->whereHas('latestStatusLog', function ($query) {
                $query->where('status', 'RECORDED');
            })
            ->latest()
            ->paginate(5);
    }

    // --- ACTION (LOGIKA WIZARD) ---
    public function approveAction(): Action
    {
        return Action::make('approveAction')
            ->label('Verifikasi')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation(false)
            ->steps([
                Step::make('Konfirmasi Pengambilan')
                    ->schema([
                        Toggle::make('is_picked_up_by_relative')
                            ->label('Apakah barang ini akan diambil oleh kerabat?')
                            ->onIcon('heroicon-o-check')
                            ->offIcon('heroicon-o-x-mark')
                            ->live()
                            ->default(false),
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
                            ->label('Foto Pengambil')
                            ->image()
                            ->disk('public')
                            ->directory('pickup-photos')
                            ->required(),
                        FileUpload::make('photo_of_identity_path')
                            ->label('Foto Identitas')
                            ->image()
                            ->disk('public')
                            ->directory('identity-photos')
                            ->required(),
                    ])
                    ->visible(fn (Get $get) => $get('is_picked_up_by_relative')),
            ])
            ->action(function (array $data, array $arguments) {
                // 1. Ambil Record
                $recordId = $arguments['record'] ?? null;
                $record = ConfiscatedItem::find($recordId);

                if (!$record) {
                    Notification::make()->title('Error')->body('Barang tidak ditemukan.')->danger()->send();
                    return;
                }

                // 2. Logika Cabang
                if (!empty($data['is_picked_up_by_relative']) && $data['is_picked_up_by_relative'] == true) {
                    // OPSI A: Diambil Kerabat
                    $record->pickups()->create([
                        'pickup_by_name' => $data['pickup_by_name'],
                        'pickup_by_identity_number' => $data['pickup_by_identity_number'],
                        'relationship_to_passenger' => $data['relationship_to_passenger'],
                        'photo_of_recipient_path' => $data['photo_of_recipient_path'],
                        'photo_of_identity_path' => $data['photo_of_identity_path'],
                        'verified_by_user_id' => Auth::id(),
                        'pickup_timestamp' => now(),
                    ]);

                    $status = 'PENDING_PICKUP';
                    $notes = 'Menunggu pengambilan oleh kerabat: ' . $data['pickup_by_name'];

                } else {
                    // OPSI B: Masuk Gudang
                    $status = 'IN_STORAGE'; // Pastikan huruf besar sesuai ENUM di database
                    $notes = 'Barang diverifikasi untuk disimpan di gudang.';
                }

                // 3. Create Log Status (PERBAIKAN UTAMA DI SINI)
                ItemStatusLog::create([
                    'item_id' => $record->id,
                    'user_id' => Auth::id(), // Ganti 'changed_by' menjadi 'user_id'
                    'status'  => $status,
                    'notes'   => $notes,
                    // Hapus 'recorded_at', biarkan created_at otomatis terisi
                ]);

                Notification::make()
                    ->title('Berhasil Diverifikasi')
                    ->body($status === 'IN_STORAGE' ? 'Barang masuk ke gudang.' : 'Menunggu kerabat.')
                    ->success()
                    ->send();
            });
    }

    public static function canView(): bool
    {
        return auth()->user()->role === 'squad_leader_avsec';
    }
}