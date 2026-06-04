<?php

namespace App\Filament\Resources\ConfiscatedItems\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;

class CommunicationLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'communications';
    protected static ?string $title = 'Log Komunikasi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('communication_type')
                    ->label('Jenis Komunikasi')
                    ->options([
                        'general' => '💬 Umum',
                        'initial_contact' => '👋 Kontak Awal',
                        'shipment_inquiry' => '🚚 Tanya Pengiriman',
                        'payment_follow_up' => '💰 Follow Up Pembayaran',
                        'confirmation' => '✅ Konfirmasi',
                    ])
                    ->default('general')
                    ->required()
                    ->reactive()
                    ->columnSpanFull(),

                Select::make('channel')
                    ->options([
                        'whatsapp' => '💬 WhatsApp',
                        'email' => '📧 Email',
                        'phone_call' => '📞 Telepon',
                        'other' => '📝 Lainnya',
                    ])
                    ->required()
                    ->label('Media Komunikasi'),

                Select::make('communication_status')
                    ->label('Status Komunikasi')
                    ->options([
                        'sent' => '📤 Terkirim',
                        'responded' => '✅ Direspon',
                        'shipment_confirmed' => '🚚 Setuju Kirim',
                        'shipment_declined' => '❌ Tolak Kirim',
                        'payment_requested' => '💰 Menunggu Bayar',
                        'payment_confirmed' => '✅ Sudah Bayar',
                        'payment_failed' => '❌ Gagal Bayar',
                        'address_provided' => '📍 Alamat Diberikan',
                        'waiting_response' => '⏳ Menunggu Respon',
                        'completed' => '✅ Selesai',
                    ])
                    ->default('sent')
                    ->required()
                    ->reactive(),

                DateTimePicker::make('sent_at')
                    ->label('Waktu Komunikasi')
                    ->default(now())
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('message_summary')
                    ->label('Ringkasan Pesan/Komunikasi')
                    ->required()
                    ->columnSpanFull()
                    ->rows(3)
                    ->helperText('Contoh: Penumpang ditanya apakah ingin barang dikirim.'),

                Toggle::make('response_received')
                    ->label('Sudah Ada Respon?')
                    ->default(false)
                    ->reactive()
                    ->columnSpanFull(),

                DateTimePicker::make('responded_at')
                    ->label('Waktu Respon')
                    ->visible(fn (callable $get) => $get('response_received'))
                    ->required(fn (callable $get) => $get('response_received')),

                Textarea::make('response_notes')
                    ->label('Catatan Respon')
                    ->visible(fn (callable $get) => $get('response_received'))
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Contoh: Penumpang setuju, akan transfer besok.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('communication_type')
                    ->label('Jenis')
                    ->formatStateUsing(fn ($record) => $record->type_label)
                    ->sortable(),

                TextColumn::make('channel')
                    ->label('Media')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'whatsapp' => 'success',
                        'email' => 'info',
                        'phone_call' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('communication_status')
                    ->label('Status')
                    ->formatStateUsing(fn ($record) => $record->status_label)
                    ->badge()
                    ->color(fn ($record) => $record->status_color)
                    ->sortable(),

                TextColumn::make('response_received')
                    ->label('Respon')
                    ->formatStateUsing(fn (bool $state) => $state ? '✅ Ya' : '⏳ Belum')
                    ->badge()
                    ->color(fn (bool $state) => $state ? 'success' : 'warning'),

                TextColumn::make('user.name')
                    ->label('Dicatat Oleh')
                    ->toggleable(),

                TextColumn::make('message_summary')
                    ->label('Ringkasan')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->message_summary),

                TextColumn::make('sent_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('responded_at')
                    ->label('Respon Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('communication_type')
                    ->label('Jenis Komunikasi')
                    ->options([
                        'general' => 'Umum',
                        'initial_contact' => 'Kontak Awal',
                        'shipment_inquiry' => 'Tanya Pengiriman',
                        'payment_follow_up' => 'Follow Up Bayar',
                        'confirmation' => 'Konfirmasi',
                    ]),

                SelectFilter::make('communication_status')
                    ->label('Status')
                    ->options([
                        'sent' => 'Terkirim',
                        'waiting_response' => 'Menunggu Respon',
                        'shipment_confirmed' => 'Setuju Kirim',
                        'shipment_declined' => 'Tolak Kirim',
                        'payment_requested' => 'Menunggu Bayar',
                        'payment_confirmed' => 'Sudah Bayar',
                    ]),

                SelectFilter::make('response_received')
                    ->label('Status Respon')
                    ->options([
                        '1' => 'Sudah Direspon',
                        '0' => 'Belum Direspon',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Catat Komunikasi Baru')
                    ->icon('heroicon-o-plus-circle')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                // Quick action: Mark as Responded
                Action::make('markResponded')
                    ->label('Tandai Direspon')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => !$record->response_received)
                    ->form([
                        Textarea::make('response_notes')
                            ->label('Catatan Respon')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->markAsResponded($data['response_notes']);
                        Notification::make()
                            ->title('Respon Dicatat')
                            ->success()
                            ->send();
                    }),

                // Quick action: Mark Shipment Confirmed
                Action::make('confirmShipment')
                    ->label('Setuju Kirim')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->visible(fn ($record) => 
                        $record->communication_type === 'shipment_inquiry' && 
                        !in_array($record->communication_status, ['shipment_confirmed', 'shipment_declined'])
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->markShipmentConfirmed();
                        Notification::make()
                            ->title('Status Diupdate: Setuju Kirim')
                            ->success()
                            ->send();
                    }),

                // Quick action: Mark Payment Confirmed
                Action::make('confirmPayment')
                    ->label('Bayar Terkonfirmasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => 
                        $record->communication_status === 'payment_requested'
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->markPaymentConfirmed();
                        Notification::make()
                            ->title('Pembayaran Terkonfirmasi')
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->label('Edit'),
                    
                DeleteAction::make(),
            ])
            ->defaultSort('sent_at', 'desc');
    }
}