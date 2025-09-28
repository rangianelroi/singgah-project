<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->required()->label('Nama Lengkap'),
                TextInput::make('employee_id')
                ->required()->unique(ignoreRecord: true)->label('NIP'),
                TextInput::make('email')
                ->email()->required()->unique(ignoreRecord: true),
                Select::make('role')
                ->options([
                    'admin' => 'Admin',
                    'operator_avsec' => 'Operator AVSEC',
                    'squad_leader_avsec' => 'Squad Leader AVSEC',
                    'team_leader_avsec' => 'Team Leader AVSEC',
                    'department_head_avsec' => 'Dept Head AVSEC',
                ])
                ->required(),
                TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context): bool => $context === 'create')
                ->label('Password Baru'),
            ]);
    }
}
