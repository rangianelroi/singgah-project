<?php

namespace App\Filament\Resources\ConfiscatedItems\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'statusLogs';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('item_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->recordTitleAttribute('status')
        ->columns([
            TextColumn::make('status')
                ->badge(),

            // Menampilkan nama user yang melakukan aksi
            TextColumn::make('user.name')
                ->label('Dilakukan Oleh'),

            TextColumn::make('notes')
                ->label('Catatan'),

            TextColumn::make('created_at')
                ->dateTime('d M Y H:i')
                ->label('Waktu'),
        ])
        ->headerActions([
            // Nanti kita akan buat form untuk menambah status manual di sini
        ])
        
        ->filters([
            //
        ])

        ->headerActions([
            CreateAction::make(),
            AssociateAction::make(),
        ])
        ->recordActions([
            EditAction::make(),
            DissociateAction::make(),
            DeleteAction::make(),
        ])
        ->toolbarActions([
            BulkActionGroup::make([
                DissociateBulkAction::make(),
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}
