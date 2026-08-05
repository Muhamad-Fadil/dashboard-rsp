<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin — kelola semua, tidak terikat divisi
        User::updateOrCreate(
            ['email' => 'admin@rspgoenawan.co.id'],
            [
                'name' => 'Admin Sistem',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'division_id' => null,
            ]
        );

        // Direktur — akses semua dashboard, tidak terikat divisi
        User::updateOrCreate(
            ['email' => 'direktur@rspgoenawan.co.id'],
            [
                'name' => 'Direktur RSP Goenawan',
                'password' => Hash::make('password'),
                'role' => 'direktur',
                'division_id' => null,
            ]
        );

        // Manajer & Operator — masing-masing 1 akun per divisi
        $divisions = Division::all();

        foreach ($divisions as $division) {
            User::updateOrCreate(
                ['email' => 'manajer.' . $division->slug . '@rspgoenawan.co.id'],
                [
                    'name' => 'Manajer ' . $division->name,
                    'password' => Hash::make('password'),
                    'role' => 'manajer',
                    'division_id' => $division->id,
                ]
            );

            User::updateOrCreate(
                ['email' => 'operator.' . $division->slug . '@rspgoenawan.co.id'],
                [
                    'name' => 'Operator ' . $division->name,
                    'password' => Hash::make('password'),
                    'role' => 'operator',
                    'division_id' => $division->id,
                ]
            );
        }
    }
}
