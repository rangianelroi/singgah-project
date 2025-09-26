<?php

namespace App\Filament\Resources\Airlines\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AirlineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Maskapai'),
                TextInput::make('code')
                    ->required()
                    ->maxLength(10)
                    ->label('Kode IATA'),
            ]);
    }
}
