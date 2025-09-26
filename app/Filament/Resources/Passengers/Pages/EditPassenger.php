<?php

namespace App\Filament\Resources\Passengers\Pages;

use App\Filament\Resources\Passengers\PassengerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPassenger extends EditRecord
{
    protected static string $resource = PassengerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
