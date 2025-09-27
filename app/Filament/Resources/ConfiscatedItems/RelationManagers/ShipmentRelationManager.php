<?php

namespace App\Filament\Resources\ConfiscatedItems\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;


class ShipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'shipment';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        // Hanya tampilkan jika peran user sesuai
        $userRole = auth()->user()->role;
        return in_array($userRole, ['team_leader_avsec', 'admin']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('recipient_name')->required()->label('Nama Penerima'),
                TextInput::make('recipient_phone')->tel()->required()->label('No. Telepon Penerima'),
                Textarea::make('street_address')->required()->label('Alamat rumah')->columnSpanFull(),
                TextInput::make('subdistrict')->required()->label('Kelurahan'), 
                TextInput::make('district')->required()->label('Kecamatan'),
                TextInput::make('city')->required()->label('Kota'),
                TextInput::make('province')->required()->label('Provinsi'),
                TextInput::make('country')->required()->label('country'),
                TextInput::make('postal_code')->required()->label('Kode Pos'),
                TextInput::make('tracking_number')->label('Nomor Resi'),
                Select::make('payment_status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'])
                    ->default('pending')->required(),
                TextInput::make('shipping_cost')->numeric()->prefix('Rp')->label('Ongkos Kirim'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('recipient_name')
            ->columns([
                TextColumn::make('recipient_name')->label('Nama Penerima'),
                TextColumn::make('tracking_number')->label('No. Resi'),
                TextColumn::make('payment_status')->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->hidden(fn (RelationManager $livewire): bool => $livewire->getOwnerRecord()->shipment()->exists()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
