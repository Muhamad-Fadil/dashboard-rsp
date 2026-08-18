<?php

namespace Database\Seeders;

use App\Models\OperatorSubmenuAkses;
use App\Models\User;
use Illuminate\Database\Seeder;

class OperatorSubmenuAksesSeeder extends Seeder
{
    public function run(): void
    {
        $operatorLayanan = User::where('email', 'operator.layanan@rspgoenawan.co.id')->first();

        if (! $operatorLayanan) {
            return;
        }

        foreach (array_keys(User::daftarSubmenuLayanan()) as $submenu) {
            OperatorSubmenuAkses::updateOrCreate([
                'user_id' => $operatorLayanan->id,
                'submenu' => $submenu,
            ]);
        }
    }
}