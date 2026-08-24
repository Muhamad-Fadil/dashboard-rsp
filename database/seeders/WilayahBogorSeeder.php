<?php

namespace Database\Seeders;

use App\Models\WilayahBogor;
use Illuminate\Database\Seeder;

class WilayahBogorSeeder extends Seeder
{
    public function run(): void
    {
        $kabupatenBogor = [
            ['32.01.01', 'Cibinong'], ['32.01.02', 'Gunung Putri'], ['32.01.03', 'Citeureup'],
            ['32.01.04', 'Sukaraja'], ['32.01.05', 'Babakan Madang'], ['32.01.06', 'Jonggol'],
            ['32.01.07', 'Cileungsi'], ['32.01.08', 'Cariu'], ['32.01.09', 'Sukamakmur'],
            ['32.01.10', 'Parung'], ['32.01.11', 'Gunung Sindur'], ['32.01.12', 'Kemang'],
            ['32.01.13', 'Bojonggede'], ['32.01.14', 'Leuwiliang'], ['32.01.15', 'Ciampea'],
            ['32.01.16', 'Cibungbulang'], ['32.01.17', 'Pamijahan'], ['32.01.18', 'Rumpin'],
            ['32.01.19', 'Jasinga'], ['32.01.20', 'Parung Panjang'], ['32.01.21', 'Nanggung'],
            ['32.01.22', 'Cigudeg'], ['32.01.23', 'Tenjo'], ['32.01.24', 'Ciawi'],
            ['32.01.25', 'Cisarua'], ['32.01.26', 'Megamendung'], ['32.01.27', 'Caringin'],
            ['32.01.28', 'Cijeruk'], ['32.01.29', 'Ciomas'], ['32.01.30', 'Dramaga'],
            ['32.01.31', 'Tamansari'], ['32.01.32', 'Klapanunggal'], ['32.01.33', 'Ciseeng'],
            ['32.01.34', 'Rancabungur'], ['32.01.35', 'Sukajaya'], ['32.01.36', 'Tanjungsari'],
            ['32.01.37', 'Tajurhalang'], ['32.01.38', 'Cigombong'], ['32.01.39', 'Leuwisadeng'],
            ['32.01.40', 'Tenjolaya'],
        ];

        $kotaBogor = [
            ['32.71.01', 'Bogor Selatan'], ['32.71.02', 'Bogor Timur'], ['32.71.03', 'Bogor Tengah'],
            ['32.71.04', 'Bogor Barat'], ['32.71.05', 'Bogor Utara'], ['32.71.06', 'Tanah Sareal'],
        ];

        foreach ($kabupatenBogor as [$kode, $nama]) {
            WilayahBogor::updateOrCreate(['kode_wilayah' => $kode], [
                'nama_kecamatan' => $nama,
                'kabupaten_kota' => 'kabupaten',
            ]);
        }

        foreach ($kotaBogor as [$kode, $nama]) {
            WilayahBogor::updateOrCreate(['kode_wilayah' => $kode], [
                'nama_kecamatan' => $nama,
                'kabupaten_kota' => 'kota',
            ]);
        }
    }
}