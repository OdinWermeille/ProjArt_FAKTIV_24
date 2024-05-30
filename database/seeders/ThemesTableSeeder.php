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
        $themes = ['Histoire', 'Art', 'Nature', 'Architecture'];

        foreach ($themes as $theme) {
            Theme::create(['nom' => $theme]);
        }
    }
}
