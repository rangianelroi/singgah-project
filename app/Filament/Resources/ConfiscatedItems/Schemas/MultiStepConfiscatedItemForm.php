<?php

namespace App\Filament\Resources\ConfiscatedItems\Schemas;

use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\Airline;
use App\Models\Airport;

class MultiStepConfiscatedItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // ==========================================
                    // STEP 1: FLIGHT INFORMATION
                    // ==========================================
                    Step::make('Informasi Penerbangan')
                        ->description('Daftarkan data penerbangan baru')
                        ->icon('heroicon-o-paper-airplane')
                        ->schema([
                            Select::make('new_flight.airline_id')
                                ->label('Maskapai')
                                ->options(Airline::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            TextInput::make('new_flight.flight_number')
                                ->required()
                                ->label('Nomor Penerbangan')
                                ->placeholder('Contoh: 2150'),
                            Select::make('new_flight.origin_airport_id')
                                ->label('Bandara Asal')
                                ->options(Airport::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            Select::make('new_flight.destination_airport_id')
                                ->label('Bandara Tujuan')
                                ->options(Airport::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            DateTimePicker::make('new_flight.departure_time')
                                ->required()
                                ->label('Waktu Keberangkatan'),
                        ])->columns(2),

                    // ==========================================
                    // STEP 2: PASSENGER INFORMATION
                    // ==========================================
                    Step::make('Informasi Penumpang')
                        ->description('Daftarkan data penumpang baru')
                        ->icon('heroicon-o-user')
                        ->schema([
                            TextInput::make('new_passenger.full_name')
                                ->required()
                                ->label('Nama Lengkap')
                                ->columnSpanFull(),
                            TextInput::make('new_passenger.identity_number')
                                ->required()
                                ->label('Nomor Identitas (KTP/Paspor)')
                                ->columnSpanFull(),
                            TextInput::make('new_passenger.phone_number')
                                ->tel()
                                ->required()
                                ->label('Nomor Telepon')
                                ->columnSpanFull(),
                            TextInput::make('new_passenger.email')
                                ->email()
                                ->required()
                                ->label('Email')
                                ->columnSpanFull(),
                            FileUpload::make('new_passenger.identity_image_path')
                                ->image()
                                ->required()
                                ->label('Foto Identitas')
                                ->disk('local')
                                ->directory('passenger_identities')
                                ->columnSpanFull(),
                            FileUpload::make('new_passenger.boardingpass_image_path')
                                ->image()
                                ->required()
                                ->label('Foto Boarding Pass')
                                ->disk('local')
                                ->directory('passenger_boardingpasses')
                                ->columnSpanFull(),
                        ]),

                    // ==========================================
                    // STEP 3: CONFISCATED ITEM INFORMATION
                    // ==========================================
                    Step::make('Informasi Barang Sitaan')
                        ->description('Lengkapi detail barang yang disita')
                        ->icon('heroicon-o-archive-box')
                        ->schema([
                            TextInput::make('item_name')
                                ->required()
                                ->label('Nama Barang')
                                ->columnSpanFull(),
                            FileUpload::make('item_image_path')
                                ->image()
                                ->label('Gambar Barang')
                                ->disk('local')
                                ->directory('confiscated_items')
                                ->columnSpanFull(),
                            Select::make('category')
                                ->options([
                                    'dangerous_goods' => 'Dangerous Goods',
                                    'prohibited_items' => 'Prohibited Items',
                                    'security_items' => 'Security Items',
                                    'other' => 'Other',
                                ])
                                ->required()
                                ->label('Kategori Barang')
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
                            DateTimePicker::make('confiscation_date')
                                ->label('Tanggal Penyitaan')
                                ->default(now())
                                ->columnSpanFull(),
                            Textarea::make('notes')
                                ->label('Catatan Tambahan')
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull(),
            ]);
    }
}
