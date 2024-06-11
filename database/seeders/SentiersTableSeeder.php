<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SentiersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sentiers')->insert([
            [
                'nom' => 'Sentier du Lac Léman',
                'image_url' => 'storage\images\Lac_Leman.jpg',
                'description' => 'Un magnifique sentier longeant le lac Léman avec des vues imprenables sur les Alpes et de nombreux points d\'intérêt historiques et culturels. Ce sentier offre une expérience inoubliable le long des rives du lac, avec des points de vue spectaculaires et des arrêts dans des lieux emblématiques de Lausanne. Le parcours est idéal pour une promenade tranquille tout en profitant de la beauté naturelle et de l\'histoire locale.',
                'longueur' => 10.0,
                'duree' => 180,
                'user_id' => 1,
                'theme_id' => 2,
                'created_at' => '2024-06-05 12:33:00',
                'updated_at' => '2024-06-05 12:33:00',
            ],
            [
                'nom' => 'Sentier des Parcs de Lausanne',
                'image_url' => 'storage\images\Parc_Lausanne2.png',
                'description' => 'Un agréable sentier urbain traversant plusieurs parcs de Lausanne, parfait pour une promenade relaxante en ville. Découvrez les espaces verts de la ville tout en profitant de la tranquillité et de la beauté naturelle des parcs de Lausanne. Ce sentier est idéal pour les familles et les amateurs de nature souhaitant s\'échapper de l\'agitation urbaine.',
                'longueur' => 5.0,
                'duree' => 90,
                'user_id' => 1,
                'theme_id' => 4,
                'created_at' => '2024-06-04 12:33:00',
                'updated_at' => '2024-06-04 12:33:00',
            ],
            [
                'nom' => 'Sentier Culturel de Lausanne',
                'image_url' => 'storage\images\Culture_Lausanne.jpg',
                'description' => 'Ce sentier vous emmène à travers les hauts lieux culturels de Lausanne, y compris le Musée Olympique, la Cathédrale de Lausanne, et la Fondation de l\'Hermitage. Un parcours riche en histoire et en art qui offre une expérience culturelle unique. En chemin, profitez des magnifiques vues sur la ville et le lac, et découvrez des œuvres d\'art et des monuments historiques.',
                'longueur' => 7.0,
                'duree' => 120,
                'user_id' => 1,
                'theme_id' => 3,
                'created_at' => '2024-06-05 07:33:00',
                'updated_at' => '2024-06-05 07:33:00',
            ],
            [
                'nom' => 'Sentier Nature de Sauvabelin',
                'image_url' => 'storage\images\Parc_Lausanne.jpg',
                'description' => 'Explorez le parc de Sauvabelin et ses alentours avec ce sentier nature. Parfait pour les amateurs de plein air et les familles, ce sentier offre des vues sur le lac, une tour panoramique et des aires de pique-nique. Le parcours à travers la forêt et autour du lac est idéal pour observer la faune et la flore locales.',
                'longueur' => 3.5,
                'duree' => 60,
                'user_id' => 1,
                'theme_id' => 4,
                'created_at' => '2024-06-03 15:33:00',
                'updated_at' => '2024-06-03 15:33:00',
            ],
            [
                'nom' => 'Sentier Botanique',
                'image_url' => 'storage\images\Botanique_Lausanne.jpg',
                'description' => 'Découvrez la richesse botanique de Lausanne avec ce sentier qui traverse le Jardin Botanique et d\'autres espaces verts. Une belle façon de profiter de la biodiversité locale et des paysages floraux. Ce sentier est parfait pour les amoureux de la nature et ceux qui souhaitent en apprendre davantage sur la botanique.',
                'longueur' => 4.0,
                'duree' => 75,
                'user_id' => 1,
                'theme_id' => 4,
                'created_at' => '2024-06-03 18:20:00',
                'updated_at' => '2024-06-03 18:20:00',
            ],
        ]);
    }
}
