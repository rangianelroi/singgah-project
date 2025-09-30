<?php

namespace App\Filament\Resources\ConfiscatedItems\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\DateTimePicker;



class ConfiscatedItemForm
{

    protected static ?string $model = ConfiscatedItem::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('passenger_id')
                        ->relationship('passenger', 'full_name') // Relasi ke model Passenger, tampilkan 'full_name'
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Pilih Penumpang'),
                Select::make('flight_id')
                    ->relationship('flight')
                    ->getOptionLabelFromRecordUsing(fn ($record) =>
                        "{$record->airline->code} {$record->flight_number} - {$record->destination->iata_code}"
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Penerbangan'),
                TextInput::make('item_name')
                        ->required()
                        ->columnSpanFull()
                        ->label('Nama Barang'),
                FileUpload::make('item_image_path')
                    ->image()
                    ->label('Gambar Barang')
                    ->disk('local')
                    ->directory('confiscated_items')
                    ->columnSpanFull(),
                Select::make('category')
                    ->options([
                                'dangerous_goods' => 'Dangerous goods',
                                'prohibited_items' => 'Prohibited items',
                                'security_items' => 'Security items',
                                'other' => 'Other',
                            ])
                    ->required()
                    ->label('Kategori Barang')
                    ->columnSpanFull(),
                DateTimePicker::make('confiscation_date')
                    ->label('Tanggal Pencatatan')
                    ->default(now())
                    ->columnSpanFull(),
                TextInput::make('item_quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->label('Jumlah Barang'),
                TextInput::make('item_unit')
                    ->required()
                    ->default('unit')
                    ->label('Satuan Barang'),
                Textarea::make('notes')
                    ->label('Catatan Tambahan')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('storage_location')->label('Lokasi Penyimpanan di Gudang')->visible(fn () => in_array(auth()->user()->role, ['team_leader_avsec', 'admin'])),
            ]);
    }
}
