<?php

namespace App\Filament\Resources\ConfiscatedItems;

use App\Models\ConfiscatedItem;
use App\Filament\Resources\ConfiscatedItems\Pages\CreateConfiscatedItem;
use App\Filament\Resources\ConfiscatedItems\Pages\EditConfiscatedItem;
use App\Filament\Resources\ConfiscatedItems\Pages\ListConfiscatedItems;
use App\Filament\Resources\ConfiscatedItems\Pages\ViewConfiscatedItem;
use App\Filament\Resources\ConfiscatedItems\RelationManagers\StatusLogsRelationManager;
use App\Filament\Resources\ConfiscatedItems\RelationManagers\PickupsRelationManager;
use App\Filament\Resources\ConfiscatedItems\RelationManagers\ShipmentRelationManager;
use App\Filament\Resources\ConfiscatedItems\RelationManagers\DisposalRelationManager;
use App\Filament\Resources\ConfiscatedItems\RelationManagers\CommunicationLogsRelationManager;

use App\Filament\Resources\ConfiscatedItems\Schemas\ConfiscatedItemForm;
use App\Filament\Resources\ConfiscatedItems\Tables\ConfiscatedItemsTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class ConfiscatedItemResource extends Resource
{
    protected static ?string $model = ConfiscatedItem::class;

    protected static ?string $modelLabel = 'Barang Sitaan';

    protected static ?string $navigationLabel = 'Barang Sitaan';

    protected static ?string $pluralModelLabel = 'Barang Sitaan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArchiveBox;

    public static function form(Schema $schema): Schema
    {
        return ConfiscatedItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConfiscatedItemsTable::configure($table);
        
    }

    public static function getRelations(): array
    {
        return [
            // PASTIKAN BARIS INI ADA DAN TIDAK DI-KOMENTAR
            StatusLogsRelationManager::class,
            PickupsRelationManager::class,
            ShipmentRelationManager::class,
            DisposalRelationManager::class,
            CommunicationLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConfiscatedItems::route('/'),
            'create' => CreateConfiscatedItem::route('/create'),
            'edit' => EditConfiscatedItem::route('/{record}/edit'),
            'view' => ViewConfiscatedItem::route('/{record}'),
        ];
    }
}
