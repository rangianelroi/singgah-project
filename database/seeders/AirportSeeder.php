<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AirportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('airports')->insert([
            ['id' => 1, 'name' => 'Bandara Internasional Sam Ratulangi', 'iata_code' => 'MDC', 'city' => 'Manado', 'country' => 'Indonesia'],
            ['id' => 2, 'name' => 'Bandara Internasional Soekarno–Hatta', 'iata_code' => 'CGK', 'city' => 'Tangerang', 'country' => 'Indonesia'],
            ['id' => 3, 'name' => 'Bandara Internasional Juanda', 'iata_code' => 'SUB', 'city' => 'Surabaya', 'country' => 'Indonesia'],
            ['id' => 4, 'name' => 'Bandara Internasional Ngurah Rai', 'iata_code' => 'DPS', 'city' => 'Denpasar', 'country' => 'Indonesia'],
            ['id' => 5, 'name' => 'Bandara Internasional Sultan Hasanuddin', 'iata_code' => 'UPG', 'city' => 'Makassar', 'country' => 'Indonesia'],
            ['id' => 6, 'name' => 'Bandara Internasional Kualanamu', 'iata_code' => 'KNO', 'city' => 'Medan', 'country' => 'Indonesia'],
            ['id' => 7, 'name' => 'Bandara Internasional Adisutjipto', 'iata_code' => 'JOG', 'city' => 'Yogyakarta', 'country' => 'Indonesia'],
            ['id' => 8, 'name' => 'Bandara Internasional Sultan Aji Muhammad Sulaiman Sepinggan', 'iata_code' => 'BPN', 'city' => 'Balikpapan', 'country' => 'Indonesia'],
            ['id' => 9, 'name' => 'Bandara Internasional Minangkabau', 'iata_code' => 'PDG', 'city' => 'Padang', 'country' => 'Indonesia'],
            ['id' => 10, 'name' => 'Bandara Internasional Husein Sastranegara', 'iata_code' => 'BDO', 'city' => 'Bandung', 'country' => 'Indonesia'],
            ['id' => 11, 'name' => 'Bandara Internasional Sultan Mahmud Badaruddin II', 'iata_code' => 'PLM', 'city' => 'Palembang', 'country' => 'Indonesia'],
            ['id' => 12, 'name' => 'Bandara Internasional Achmad Yani', 'iata_code' => 'SRG', 'city' => 'Semarang', 'country' => 'Indonesia'],
            ['id' => 13, 'name' => 'Bandara Internasional Lombok', 'iata_code' => 'LOP', 'city' => 'Lombok', 'country' => 'Indonesia'],
            ['id' => 14, 'name' => 'Bandara Internasional Silangit', 'iata_code' => 'DTB', 'city' => 'Tapanuli Utara', 'country' => 'Indonesia'],
            ['id' => 15, 'name' => 'Bandara Internasional Yogyakarta', 'iata_code' => 'YIA', 'city' => 'Yogyakarta', 'country' => 'Indonesia'],
        ]);

    }
}
