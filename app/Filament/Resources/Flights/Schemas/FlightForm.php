<?php

namespace App\Filament\Resources\Flights\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FlightForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('airline_id')
                ->relationship('airline', 'name')
                ->required()
                ->label('Maskapai')
                ->searchable()
                ->preload(),
                TextInput::make('flight_number')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->label('Nomor Penerbangan'),
                Select::make('origin_airport_id')
                    ->relationship('origin', 'name')
                    ->required()
                    ->label('Bandara Asal'),
                Select::make('destination_airport_id')
                    ->relationship('destination', 'name')
                    ->required()
                    ->label('Bandara Tujuan'),
                DateTimePicker::make('departure_time')
                    ->required()
                    ->label('Waktu Keberangkatan'),
            ])->columns(2);
    }
}
