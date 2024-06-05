<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Endroit;
use App\Models\Sentier;
use Illuminate\Support\Facades\DB;

class EndroitsSentiersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $endroitsSentiers = [
            // Sentier du Lac Léman
            ['endroit_id' => 1, 'sentier_id' => 1],
            ['endroit_id' => 2, 'sentier_id' => 1],
            ['endroit_id' => 3, 'sentier_id' => 1],
            ['endroit_id' => 10, 'sentier_id' => 1],

            // Sentier des Parcs de Lausanne
            ['endroit_id' => 7, 'sentier_id' => 2],
            ['endroit_id' => 8, 'sentier_id' => 2],
            ['endroit_id' => 9, 'sentier_id' => 2],
            ['endroit_id' => 11, 'sentier_id' => 2],

            // Sentier Culturel de Lausanne
            ['endroit_id' => 1, 'sentier_id' => 3],
            ['endroit_id' => 2, 'sentier_id' => 3],
            ['endroit_id' => 3, 'sentier_id' => 3],
            ['endroit_id' => 7, 'sentier_id' => 3],

            // Sentier Nature de Sauvabelin
            ['endroit_id' => 5, 'sentier_id' => 4],
            ['endroit_id' => 8, 'sentier_id' => 4],
            ['endroit_id' => 12, 'sentier_id' => 4],

            // Sentier Botanique
            ['endroit_id' => 12, 'sentier_id' => 5],
            ['endroit_id' => 13, 'sentier_id' => 5],
            ['endroit_id' => 6, 'sentier_id' => 5],
            ['endroit_id' => 4, 'sentier_id' => 5],
        ];

        foreach ($endroitsSentiers as $endroitSentier) {
            DB::table('endroit_sentier')->insert([
                'endroit_id' => $endroitSentier['endroit_id'],
                'sentier_id' => $endroitSentier['sentier_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
