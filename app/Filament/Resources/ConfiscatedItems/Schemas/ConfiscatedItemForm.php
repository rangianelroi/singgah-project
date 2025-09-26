<?php

namespace App\Filament\Resources\ConfiscatedItems\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ConfiscatedItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Penumpang')
                    ->schema([
                        Select::make('passenger_id')
                            ->relationship('passenger', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Penumpang'),
                        Select::make('flight_id')
                            ->relationship('flight') // Hapus argumen kedua
                            // Buat label kustom untuk setiap pilihan
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                "{$record->airline->code} {$record->flight_number} - {$record->destination->iata_code}"
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Penerbangan'),
                    ])->columns(2)->columnSpanFull(),
                
                Section::make('Detail Barang Tertahan')
                    ->schema([
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
                            ->required()
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
                    ])->columns(2) ->columnSpanFull(),
            ]);
    }
}
