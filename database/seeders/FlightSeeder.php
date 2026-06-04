<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class FlightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $airlineIds = DB::table('airlines')->pluck('id')->toArray();
        $airportIds = DB::table('airports')->pluck('id')->toArray();

        for ($i = 0; $i < 15; $i++) {
            $originId = $airportIds[array_rand($airportIds)];
            $destinationId = $airportIds[array_rand($airportIds)];
            
            // Pastikan origin dan destination berbeda
            while ($destinationId === $originId) {
                $destinationId = $airportIds[array_rand($airportIds)];
            }

            DB::table('flights')->insert([
                'airline_id' => $airlineIds[array_rand($airlineIds)],
                'flight_number' => strtoupper($faker->bothify('??-####')),
                'origin_airport_id' => $originId,
                'destination_airport_id' => $destinationId,
                'departure_time' => Carbon::now()->addDays(rand(1, 30))->setHour(rand(6, 22))->setMinute(0)->setSecond(0),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
