<?php

namespace App\Filament\Resources\ConfiscatedItems\Pages;

use App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConfiscatedItem extends EditRecord
{
    protected static string $resource = ConfiscatedItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
