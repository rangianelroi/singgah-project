<?php

namespace App\Filament\Resources\Airports\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AirportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Bandara'),
                TextInput::make('iata_code')
                    ->required()
                    ->maxLength(10)
                    ->label('Kode IATA'),
                TextInput::make('city')
                    ->required()
                    ->maxLength(255)
                    ->label('Kota'),
                TextInput::make('country')
                    ->required()
                    ->maxLength(255)
                    ->label('Negara'),
            ]);
    }
}
