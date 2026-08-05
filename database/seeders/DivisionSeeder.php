<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['name' => 'Layanan', 'slug' => 'layanan'],
            ['name' => 'SDM', 'slug' => 'sdm'],
            ['name' => 'Keuangan', 'slug' => 'keuangan'],
        ];

        foreach ($divisions as $division) {
            Division::updateOrCreate(['slug' => $division['slug']], $division);
        }
    }
}
