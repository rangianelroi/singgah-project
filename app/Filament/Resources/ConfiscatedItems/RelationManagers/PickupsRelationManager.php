<?php

namespace App\Filament\Resources\ConfiscatedItems\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;


class PickupsRelationManager extends RelationManager
{
    protected static string $relationship = 'pickups';

    // Mengatur hak akses: hanya tampil jika peran sesuai
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        $userRole = auth()->user()->role;
        return in_array($userRole, ['squad_leader_avsec', 'team_leader_avsec', 'admin']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('pickup_by_name')
                    ->required()
                    ->label('Nama Pengambil'),
                TextInput::make('pickup_by_identity_number')
                    ->required()
                    ->label('No. Identitas Pengambil'),
                TextInput::make('relationship_to_passenger')
                    ->required()
                    ->label('Hubungan dengan Penumpang'),
                FileUpload::make('photo_of_recipient_path')
                    ->label('Foto Penerima')
                    ->image()
                    ->disk('local')
                    ->directory('pickup_photos')
                    ->required(),
                FileUpload::make('photo_of_identity_path')
                    ->label('Foto Identitas')
                    ->image()
                    ->disk('local')
                    ->directory('identity_photos')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pickup_by_name')
            ->columns([
                TextColumn::make('pickup_by_name')->label('Nama Pengambil')->searchable(),
                TextColumn::make('relationship_to_passenger')->label('Hubungan dengan Penumpang'),
                TextColumn::make('pickup_timestamp')->dateTime('d M Y H:i')->label('Waktu Pengambilan'),
                TextColumn::make('verifiedBy.name')->label('Diverifikasi Oleh'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),
                Action::make('confirm_handover')
                ->label('Konfirmasi Serah Terima')
                ->color('success')
                ->icon('heroicon-o-check-badge')
                ->requiresConfirmation()
                ->action(function (RelationManager $livewire, Model $record) {
                    // Buat log status final di data induk (ConfiscatedItem)
                    $livewire->getOwnerRecord()->statusLogs()->create([
                        'status' => 'PICKED_UP',
                        'user_id' => auth()->id(),
                        'notes' => 'Barang telah diserahkan kepada kerabat: ' . $record->pickup_by_name,
                    ]);
                })
                // Tombol ini hanya muncul jika status barang utama adalah 'PENDING_PICKUP'
                ->visible(function (RelationManager $livewire): bool {
                    $latestStatus = $livewire->getOwnerRecord()->statusLogs()->latest()->first();
                    return $latestStatus?->status === 'PENDING_PICKUP';
                }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
