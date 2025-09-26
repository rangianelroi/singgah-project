<?php

namespace App\Filament\Resources\Flights\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FlightsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_flight_number')
                    ->searchable()
                    ->sortable()
                    ->label('Nomor Penerbangan'),
                TextColumn::make('airline.name')
                    ->label('Maskapai')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('origin.name')
                    ->label('Bandara Asal')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('destination.name')
                    ->label('Bandara Tujuan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('departure_time')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->label('Waktu Keberangkatan'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
