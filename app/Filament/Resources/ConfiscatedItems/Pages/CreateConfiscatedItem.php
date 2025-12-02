<?php

namespace App\Filament\Resources\ConfiscatedItems\Pages;

use App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource;
use App\Filament\Resources\ConfiscatedItems\Schemas\MultiStepConfiscatedItemForm;
use App\Models\Flight;
use App\Models\Passenger;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateConfiscatedItem extends CreateRecord
{
    protected static string $resource = ConfiscatedItemResource::class;
    
    /**
     * Override form() untuk menggunakan MultiStepConfiscatedItemForm
     */
    public function form(Schema $schema): Schema
    {
        return MultiStepConfiscatedItemForm::configure($schema);
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by_user_id'] = auth()->id();
        
        // Jika user membuat penerbangan baru, buat di database terlebih dahulu
        if (!empty($data['new_flight']) && empty($data['flight_id'])) {
            $flight = Flight::create($data['new_flight']);
            $data['flight_id'] = $flight->id;
        }
        
        // Jika user membuat penumpang baru, buat di database terlebih dahulu
        if (!empty($data['new_passenger']) && empty($data['passenger_id'])) {
            $passenger = Passenger::create($data['new_passenger']);
            $data['passenger_id'] = $passenger->id;
        }
        
        // Hapus array 'new_flight' dan 'new_passenger' karena sudah tidak diperlukan
        unset($data['new_flight']);
        unset($data['new_passenger']);
 
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