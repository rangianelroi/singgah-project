<?php

namespace App\Filament\Resources\ConfiscatedItems\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;


class ConfiscatedItemForm
{

    protected static ?string $model = ConfiscatedItem::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Penumpang')
                    ->schema([
                        Placeholder::make('passenger_name')->label('Nama Lengkap')->content(fn ($record) => $record->passenger?->full_name),
                        Placeholder::make('passenger_identity')->label('No. Identitas')->content(fn ($record) => $record->passenger?->identity_number),
                        Placeholder::make('flight_details')
                        ->label('Nomor Penerbangan')
                        ->content(fn ($record) => $record->flight?->fullFlightNumber), 
                    ])->columnSpanFull(),
                Section::make('Detail Barang Sitaan')
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
                            Placeholder::make('confiscation_date')
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
                    ])->columnSpanFull(),
                ]);
    }
}
