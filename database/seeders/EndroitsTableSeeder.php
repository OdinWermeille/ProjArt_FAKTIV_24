<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Endroit;
use App\Models\User;

class EndroitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            for ($i = 1; $i <= 5; $i++) {
            // Générez des coordonnées X et Y aléatoires
            $coordonneesX = rand(1, 100);
            $coordonneesY = rand(1, 100);

            // Créez un nouvel endroit pour chaque utilisateur
            Endroit::create([
                'nom' => Str::random(10), 
                'image_url' => 'https://via.placeholder.com/150', 
                'description' => Str::random(50), 
                'localite' => 'Localité'. $coordonneesX,
                'coordonneesX' => $coordonneesX,
                'coordonneesY' => $coordonneesY,
                'user_id' => $user->id,
            ]);
        }
    }
}
}
