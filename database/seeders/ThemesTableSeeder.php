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
        $themes = ['Tout','Historique', 'Arts & Culture', 'Nature', 'Architecture', 'Street Art', 'Sportif', 'Gastronomie', 'Ephémère'];

        foreach ($themes as $theme) {
            Theme::create(['nom' => $theme]);
        }
    }
}