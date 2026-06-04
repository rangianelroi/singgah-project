<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfiscatedItem;
use App\Models\ItemStatusLog;
use App\Models\Passenger;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ConfiscatedItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $passengerIds = Passenger::pluck('id')->toArray();
        $flightIds = Flight::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
        $categories = ['dangerous_goods', 'prohibited_items', 'security_items', 'other'];
        $units = ['pcs', 'botol', 'pak', 'unit'];
        $locations = ['Gudang A', 'Gudang B', 'Gudang C'];
        $pendingActions = ['shipment_confirmation', 'payment_confirmation', null];
        
        // Status progression berdasarkan umur barang
        $statusProgression = [
            'RECORDED',
            'VERIFIED_BY_SQUAD_LEADER',
            'PENDING_PICKUP',
            'VERIFIED_FOR_STORAGE',
            'IN_STORAGE',
            'SHIPPED',
            'PICKED_UP',
            'DISPOSED'
        ];

        for ($i = 0; $i < 50; $i++) {
            $daysAgo = rand(0, 60);
            $confiscationDate = Carbon::now()->subDays($daysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            
            // Tentukan status berdasarkan umur barang
            $statusIndex = min(intval($daysAgo / 8), count($statusProgression) - 1);
            
            $item = ConfiscatedItem::create([
                'passenger_id' => $passengerIds[array_rand($passengerIds)],
                'flight_id' => $flightIds[array_rand($flightIds)],
                'recorded_by_user_id' => $userIds[array_rand($userIds)],
                'item_name' => 'Barang Dummy ' . ($i + 1),
                'item_image_path' => null,
                'category' => $categories[array_rand($categories)],
                'item_quantity' => rand(1, 10),
                'item_unit' => $units[array_rand($units)],
                'notes' => 'Catatan untuk barang dummy ' . ($i + 1),
                'confiscation_date' => $confiscationDate,
                'storage_location' => $locations[array_rand($locations)],
                'pending_action' => $pendingActions[array_rand($pendingActions)],
            ]);
            
            // Buat status log progression
            $currentDate = $confiscationDate->copy();
            for ($s = 0; $s <= $statusIndex; $s++) {
                ItemStatusLog::create([
                    'item_id' => $item->id,
                    'user_id' => $userIds[array_rand($userIds)],
                    'status' => $statusProgression[$s],
                    'notes' => 'Status: ' . $statusProgression[$s],
                    'created_at' => $currentDate->copy()->addHours(rand(2, 24)),
                    'updated_at' => $currentDate->copy()->addHours(rand(2, 24)),
                ]);
                $currentDate->addDays(rand(2, 5));
            }
        }
    }
}
