<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Theme;

class ThemesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        for ($i = 1; $i <= 5; $i++) {
            Theme::create(['nom' => 'Theme ' . $i
                        ]);
        }
    }
}
