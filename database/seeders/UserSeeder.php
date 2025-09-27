<?php

namespace Database\Seeders; 

use Illuminate\Database\Seeder;
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
            // 4. Team Leader Investigasi
            [
                'name' => 'Team Leader Investigasi 01',
                'employee_id' => 'TLI01',
                'email' => 'investigasi@singgah.test',
                'password' => Hash::make('password'),
                'role' => 'team_leader_avsec',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 5. Dept Head AVSEC
            [
                'name' => 'Dept Head AVSEC',
                'employee_id' => 'DHAVSEC01',
                'email' => 'depthead@singgah.test',
                'password' => Hash::make('password'),
                'role' => 'department_head_avsec',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}