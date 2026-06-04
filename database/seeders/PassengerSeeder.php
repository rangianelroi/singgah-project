p<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PassengerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 20; $i++) {
            DB::table('passengers')->insert([
                'full_name' => $faker->name(),
                'identity_number' => $faker->unique()->numerify('##########'),
                'phone_number' => $faker->phoneNumber(),
                'email' => $faker->unique()->safeEmail(),
                'identity_image_path' => 'images/identity/dummy_' . ($i + 1) . '.jpg',
                'boardingpass_image_path' => 'images/boardingpass/dummy_' . ($i + 1) . '.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
