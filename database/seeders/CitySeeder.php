<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cities')->truncate();

        $path = database_path('cities.csv');

        if (!file_exists($path)) {
            $this->command->error('File cities.csv tidak ditemukan!');
            return;
        }

        $handle = fopen($path, 'r');

        // Skip header
        fgetcsv($handle);

        $data = [];

        while (($row = fgetcsv($handle)) !== false) {

            // Minimal harus ada 2 kolom
            if (count($row) < 2) {
                continue;
            }

            // 🔥 FIX CSV DENGAN KOMA DI NAMA
            $island = trim(array_pop($row)); // kolom terakhir
            $name   = trim(implode(',', $row)); // gabung sisanya

            if ($name === '' || $island === '') {
                continue;
            }

            $data[] = [
                'name' => $name,
                'island' => $island,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        fclose($handle);

        DB::table('cities')->insert($data);

        $this->command->info('Cities seeded successfully!');
    }
}
