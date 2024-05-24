<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Endroit;
use App\Models\Sentier;

class EndroitsSentiersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Assurez-vous que les tables endroits et sentiers ont été seedées
        $endroits = Endroit::all();
        $sentiers = Sentier::all();

        // Supposons que chaque sentier doit être associé à 3 endroits aléatoires
        foreach ($sentiers as $sentier) {
            // Sélectionner 3 endroits aléatoires pour chaque sentier
            $endroitIds = $endroits->random(rand(3, 5))->pluck('id')->toArray();

            // Utilisez attach() pour chaque relation pivot, ce qui permet de spécifier les timestamps
            foreach ($endroitIds as $endroitId) {
                $sentier->endroits()->attach($endroitId, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
