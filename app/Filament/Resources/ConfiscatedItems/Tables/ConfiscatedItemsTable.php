<?php

namespace App\Filament\Resources\ConfiscatedItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use App\Models\ConfiscatedItem;

class ConfiscatedItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('item_image_path')
                    ->imageHeight(40)
                    ->circular()
                    ->label('Gambar Barang')
                    ->disk('local'),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge(),
                TextColumn::make('item_name')
                        ->searchable()
                        ->sortable()
                        ->label('Nama Barang'),
                TextColumn::make('storage_status.status')
                    ->badge()
                    ->color(fn ($record) => $record->storage_status['color'])
                    ->formatStateUsing(fn ($record) => $record->storage_status['status'] . ' (' . $record->storage_status['remaining'] . ' hari)')
                    ->label('Status Simpan'),
                TextColumn::make('latestStatusLog.status')
                    ->label('Status Terakhir')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'RECORDED' => 'gray',
                        'VERIFIED_BY_SQUAD_LEADER' => 'info',
                        'VERIFIED_FOR_STORAGE' => 'info',
                        'PENDING_PICKUP' => 'warning',
                        'IN_STORAGE' => 'primary',
                        'PENDING_SHIPMENT_CONFIRMATION' => 'warning',
                        'READY_TO_SHIP' => 'success',
                        'PICKED_UP' => 'success',
                        'SHIPPED' => 'success',
                        'DISPOSED' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('passenger.full_name')
                    ->label('Nama Penumpang')
                    ->searchable(),
                TextColumn::make('flight.airline.code')
                    ->label('Kode Maskapai')
                    ->badge(), 
                TextColumn::make('flight.flight_number')
                    ->label('Nomor Penerbangan'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                            // --- TAMBAHKAN BLOK AKSI BARU DI SINI ---
            Action::make('downloadDisposalReport')
                ->label('Cetak PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn (ConfiscatedItem $record) => route('disposal.report.download', $record->disposal))
                ->openUrlInNewTab()
                ->visible(function (ConfiscatedItem $record): bool {
                    // Hanya tampil jika user adalah Dept Head atau Admin
                    $hasPermission = in_array(auth()->user()->role, ['department_head_avsec', 'admin']);

                    // Dan hanya tampil jika barang sudah punya record disposal (status DISPOSED)
                    $hasDisposalRecord = $record->disposal()->exists();

                    return $hasPermission && $hasDisposalRecord;
                }),
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        // Wizard untuk membuat alur multi-langkah
                        Wizard::make([
                            Step::make('Konfirmasi Pengambilan')
                                ->schema([
                                    Toggle::make('is_picked_up_by_relative')
                                        ->label('Apakah barang ini akan diambil oleh kerabat dalam 1x24 jam?')
                                        ->onIcon('heroicon-o-check')
                                        ->offIcon('heroicon-o-x-mark')
                                        ->reactive(), // Membuat form bereaksi saat tombol ini diubah
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
                                // Langkah ini hanya akan muncul jika Toggle di atas diaktifkan
                                ->visible(fn (Get $get) => $get('is_picked_up_by_relative')),
                        ])
                    ])
                    ->action(function ($record, array $data) {
                        // Logika baru saat form di-submit
                        if ($data['is_picked_up_by_relative']) {
                            // Jika ya, buat catatan pengambilan dan log status
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
                            // Jika tidak, cukup buat log status
                            $record->statusLogs()->create([
                                'status' => 'VERIFIED_FOR_STORAGE',
                                'user_id' => auth()->id(),
                                'notes' => 'Barang diverifikasi untuk disimpan di gudang.',
                            ]);
                        }
                    })
                    ->visible(function ($record) {
                        $latestStatus = $record->statusLogs()->latest()->first();
                         $userRole = auth()->user()->role;
                        return $latestStatus?->status === 'RECORDED' && 
                        in_array($userRole, ['squad_leader_avsec', 'admin']);;
                    }),
                Action::make('whatsapp')
                    ->label('Chat WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function (ConfiscatedItem $record): ?string {
                        $passengerPhone = $record->passenger->phone_number;
                        if (!$passengerPhone) return null;
                        $message = "Selamat pagi/siang Bpk/Ibu {$record->passenger->full_name}, kami dari AVSEC Bandara Sam Ratulangi ingin menginformasikan mengenai barang Anda '{$record->item_name}'...";
                        return "https://wa.me/{$passengerPhone}?text=" . urlencode($message);
                    })
                    ->openUrlInNewTab()
                    ->visible(function (ConfiscatedItem $record): bool {
                        // Ambil peran user yang sedang login
                        $userRole = auth()->user()->role;

                        // Tombol hanya akan tampil jika:
                        // 1. Peran user adalah team_leader_avsec ATAU admin
                        // 2. DAN Penumpang memiliki nomor telepon
                        return in_array($userRole, ['team_leader_avsec', 'admin']) && !empty($record->passenger->phone_number);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
