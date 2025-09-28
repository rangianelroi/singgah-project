<?php

namespace App\Filament\Resources\ConfiscatedItems\Pages;

use App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class ViewConfiscatedItem extends ViewRecord
{
    protected static string $resource = ConfiscatedItemResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel()
            ->schema([
                Section::make('Data Penumpang')->schema([
                    TextEntry::make('passenger.full_name'),
                    TextEntry::make('passenger.identity_number'),
                ])->columnSpanFull(),
                Section::make('Detail Barang')->schema([
                    TextEntry::make('item_name'),
                    TextEntry::make('category')->badge(),
                ])->columnSpanFull(),
            ]);
    }
}
