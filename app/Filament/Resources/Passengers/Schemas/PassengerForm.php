<?php

namespace App\Filament\Resources\Passengers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PassengerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->required()
                    ->label('Nama Lengkap')
                    ->columnSpanFull(),
                TextInput::make('identity_number')
                    ->required()
                    ->label('Nomor Identitas (KTP/Paspor)')
                    ->columnSpanFull(),
                TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->label('Nomor Telepon')
                    ->columnSpanFull(),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->label('Email address')
                    ->columnSpanFull(),
                FileUpload::make('identity_image_path')
                    ->image()
                    ->required()
                    ->label('Foto Identitas (KTP/Paspor)')
                    ->disk('local')
                    ->directory('passenger_identities')
                    ->columnSpanFull(),
                FileUpload::make('boardingpass_image_path')
                    ->image()
                    ->required()
                    ->label('Foto Boarding Pass')
                    ->disk('local')
                    ->directory('passenger_boardingpasses')
                    ->columnSpanFull(),
            ]);
    }
}
