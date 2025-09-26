<?php

namespace App\Filament\Resources\ConfiscatedItems\Pages;

use App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConfiscatedItems extends ListRecords
{
    protected static string $resource = ConfiscatedItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
