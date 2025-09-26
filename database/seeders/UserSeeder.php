<?php

namespace Database\Seeders; // <-- Pastikan baris ini ada

use Illuminate\Database\Seeder; // <-- INI YANG HILANG
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Perintah untuk memasukkan data ke tabel 'users'
        DB::table('users')->insert([
            [
                'name' => 'Admin SINGGAH',
                'employee_id' => 'ADMIN001',
                'email' => 'admin@singgah.test',
                'password' => Hash::make('password'), // Password di-enkripsi
                'role' => 'admin',
                'created_at' => now(), // Mengisi waktu pembuatan
                'updated_at' => now(), // Mengisi waktu pembaruan
            ],
            [
                'name' => 'Operator AVSEC 01',
                'employee_id' => 'OPAVSEC01',
                'email' => 'operator@singgah.test',
                'password' => Hash::make('password'),
                'role' => 'operator_avsec',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Squad Leader 01',
                'employee_id' => 'SLAVSEC01',
                'email' => 'squadleader@singgah.test',
                'password' => Hash::make('password'),
                'role' => 'squad_leader_avsec',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}