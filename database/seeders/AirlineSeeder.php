<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AirlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('airlines')->insert([
            ['id' => 1, 'name' => 'Garuda Indonesia', 'code' => 'GA'],
            ['id' => 2, 'name' => 'Lion Air', 'code' => 'JT'],
            ['id' => 3, 'name' => 'Batik Air', 'code' => 'ID'],
            ['id' => 4, 'name' => 'Citilink', 'code' => 'QG'],
            ['id' => 5, 'name' => 'Sriwijaya Air', 'code' => 'SJ'],
            ['id' => 6, 'name' => 'AirAsia Indonesia', 'code' => 'QZ'],
            ['id' => 7, 'name' => 'Wings Air', 'code' => 'IW'],
            ['id' => 8, 'name' => 'Nam Air', 'code' => 'IN'],
            ['id' => 9, 'name' => 'Xpress Air', 'code' => 'XP'],
            ['id' => 10, 'name' => 'Trigana Air', 'code' => 'IL'],
        ]);
    }
}
