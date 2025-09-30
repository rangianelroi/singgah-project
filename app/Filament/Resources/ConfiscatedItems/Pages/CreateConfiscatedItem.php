<?php

namespace App\Filament\Resources\ConfiscatedItems\Pages;

use App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateConfiscatedItem extends CreateRecord
{
    protected static string $resource = ConfiscatedItemResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by_user_id'] = auth()->id();
 
        return $data;
    }

    protected function afterCreate(): void
    {
        // $this->getRecord() akan mengambil data yang baru saja dibuat
        $this->getRecord()->statusLogs()->create([
            'status' => 'RECORDED',
            'user_id' => auth()->id(),
            'notes' => 'Barang dicatat oleh petugas.',
        ]);
    }


}