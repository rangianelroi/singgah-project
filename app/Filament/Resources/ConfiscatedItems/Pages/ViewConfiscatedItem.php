<?php

namespace App\Filament\Resources\ConfiscatedItems\Pages;

use App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Group;

class ViewConfiscatedItem extends ViewRecord
{
    protected static string $resource = ConfiscatedItemResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel()
            ->schema([
                // Grid utama dengan 3 kolom
                Grid::make(4)->schema([
                    // KOLOM KIRI (mengambil 1 dari 3 kolom)
                    Section::make('Informasi Barang')
                        ->schema([
                            ImageEntry::make('item_image_path')
                                ->disk('local')
                                ->hiddenLabel()
                                ->width(170)
                                ->height('auto'),
                            TextEntry::make('item_name')->label('Nama Barang'),
                            TextEntry::make('category')->label('Kategori')->badge(),
                            TextEntry::make('latestStatusLog.status')->label('Status Terakhir')->badge(),
                            TextEntry::make('storage_location')->label('Lokasi Gudang'),
                            TextEntry::make('confiscation_date')->label('Waktu Penyitaan')->dateTime('d M Y H:i'),
                            TextEntry::make('recordedBy.name')->label('Dicatat Oleh'),
                        ])
                        ->columnSpan(2),

                    // KOLOM KANAN (mengambil 2 dari 3 kolom)
                    Grid::make(1)->schema([
                        Section::make('Detail Penumpang')
                            ->schema([
                                TextEntry::make('passenger.full_name')->label('Nama Lengkap'),
                                TextEntry::make('passenger.identity_number')->label('No. Identitas'),
                                TextEntry::make('passenger.phone_number')->label('No. Telepon'),
                                TextEntry::make('passenger.email')->label('Email'),
                            ]),
                        
                        Section::make('Informasi Penerbangan')
                            ->schema([
                                TextEntry::make('flight.full_flight_number')->label('Nomor Penerbangan'),
                                TextEntry::make('flight.airline.name')->label('Maskapai'),
                                TextEntry::make('flight.destination.name')->label('Tujuan'),
                                TextEntry::make('flight.departure_time')->label('Waktu Keberangkatan')->dateTime('d M Y H:i'),
                            ])
                    ])
                    ->columnSpan(2),
                ])
                ->columnSpan(4),
            ]);
    }
}
