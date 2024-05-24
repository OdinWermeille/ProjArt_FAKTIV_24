<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sentier;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Theme;

class SentiersTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $themes = Theme::all();

        foreach ($users as $user) {
            for ($i = 1; $i <= 2; $i++) { 
                $date = $this->randDate();
                Sentier::create([
                    'nom' => 'Nom '. $i,
                    'image_url' => 'https://via.placeholder.com/150/'. $i. '?text=Example',
                    'description' => 'Description '. $i. ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin vel auctor libero, quis venenatis augue. Curabitur a pulvinar tortor, vitae condimentum libero. Cras eu massa sed lorem mattis lacinia. Vestibulum id feugiat turpis. Proin a lorem ligula.',
                    'longueur' => rand(500, 2000), 
                    'duree' => rand(30, 120), 
                    'user_id' => $user->id,
                    'theme_id' => $themes[rand(0, count($themes)-1)]->id,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }

    private function randDate() {
        $nbJours = rand(-2800, 0);
        return Carbon::now()->addDays($nbJours);
    }
}
