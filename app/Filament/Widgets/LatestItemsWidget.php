<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;

class LatestItemsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->role === 'operator_avsec';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ConfiscatedItemResource::getEloquentQuery()->latest()->limit(5))
            ->columns([
                TextColumn::make('item_name')->label('Nama Barang'),
                TextColumn::make('created_at')->label('Waktu Catat')->since(),
            ]);
    }
}