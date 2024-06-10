<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Endroit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EndroitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('endroits')->insert([
            [
                'nom' => 'Musée Olympique',
                'image_url' => 'storage\images\Musee_olympique.jpg',
                'description' => 'Le Musée Olympique, situé à Lausanne, est un musée de renommée mondiale dédié à l\'histoire des Jeux olympiques. Il offre une plongée fascinante dans l\'univers olympique à travers des expositions interactives, des objets historiques et des présentations multimédia. Le musée est entouré de magnifiques jardins où les visiteurs peuvent admirer des sculptures et des installations artistiques inspirées par le sport. En plus des expositions permanentes, le musée propose également des événements temporaires et des activités éducatives pour tous les âges.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.508865283978466',
                'coordonneesY' => '6.634044981924916',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Cathédrale de Lausanne',
                'image_url' => 'storage\images\Cathedrale.jpg',
                'description' => 'La Cathédrale de Lausanne, un chef-d\'œuvre de l\'architecture gothique, est située au cœur de la ville de Lausanne. Construite au XIIIe siècle, elle est célèbre pour ses vitraux médiévaux, son orgue monumental et sa vue imprenable sur la ville et le lac Léman. La cathédrale est un lieu de culte actif et accueille également des concerts et des événements culturels. Les visiteurs peuvent monter jusqu\'à la tour pour profiter d\'une vue panoramique exceptionnelle sur Lausanne et ses environs.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.52257288219999',
                'coordonneesY' => '6.634876983553759',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Musée de l\'Élysée',
                'image_url' => 'storage\images\Musee_elisee.jpg',
                'description' => 'Le Musée de l\'Élysée, situé à Lausanne, est un musée de la photographie de renommée internationale. Installé dans une élégante villa du XVIIIe siècle, le musée propose des expositions temporaires et permanentes qui explorent l\'histoire et l\'art de la photographie. Le jardin du musée offre une vue splendide sur le lac Léman et les montagnes. Le musée organise également des ateliers, des conférences et des projections pour approfondir la compréhension de la photographie auprès du public.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.518537331436974',
                'coordonneesY' => '6.623877680247825',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Plateforme 10',
                'image_url' => 'storage\images\Plateforme10.jpg',
                'description' => 'Plateforme 10 est le nouveau quartier des arts de Lausanne, regroupant plusieurs institutions culturelles majeures, dont le Musée Cantonal des Beaux-Arts, le Musée de l\'Élysée et le Mudac. Ce site unique offre un espace de rencontre et de découverte pour les amateurs d\'art et de culture, avec des expositions variées et des événements tout au long de l\'année. Le complexe architectural moderne est conçu pour être un lieu dynamique, accueillant des expositions temporaires, des spectacles et des activités pour tous les publics.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.51845980811294',
                'coordonneesY' => '6.62454671103278',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Parc de Sauvabelin',
                'image_url' => 'storage\images\Sauvabelin.jpg',
                'description' => 'Le Parc de Sauvabelin est un parc boisé situé au nord de Lausanne, célèbre pour son lac pittoresque et sa tour en bois offrant une vue panoramique sur la ville et les environs. Le parc est un lieu de détente idéal pour les promenades en famille, avec des aires de pique-nique, des aires de jeux et une petite ferme pour enfants. Le lac de Sauvabelin est également populaire pour ses promenades en barque et ses sentiers de randonnée qui serpentent à travers la forêt.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.53559914449416',
                'coordonneesY' => '6.638398322167415',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Aquatis',
                'image_url' => 'storage\images\Aquatis.jpg',
                'description' => 'Aquatis est le plus grand aquarium-vivarium d\'eau douce en Europe, situé à Lausanne. Il propose une immersion dans l\'univers aquatique à travers des expositions interactives et éducatives. Les visiteurs peuvent découvrir des centaines d\'espèces aquatiques et terrestres, issues des cinq continents, et en apprendre davantage sur les écosystèmes et la conservation de la nature. Aquatis offre également des ateliers pédagogiques pour les écoles et des événements spéciaux tout au long de l\'année.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.5411499305377',
                'coordonneesY' => '6.657208540684546',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Fondation de l\'Hermitage',
                'image_url' => 'storage\images\Fondation_Hermitage.jpg',
                'description' => 'La Fondation de l\'Hermitage, située dans un parc magnifique à Lausanne, est un musée d\'art renommé qui organise des expositions temporaires de grande qualité. La villa historique abritant le musée offre un cadre élégant pour apprécier les œuvres d\'art, et le parc environnant invite à la promenade et à la détente avec ses vues splendides sur le lac Léman. En plus des expositions, la fondation propose des conférences, des visites guidées et des ateliers pour enrichir l\'expérience des visiteurs.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.52843801177699',
                'coordonneesY' => '6.637177098378441',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Espace des inventions',
                'image_url' => 'storage\images\Espace_invention.jpg',
                'description' => 'L\'Espace des inventions, situé à Lausanne, est un musée interactif conçu pour éveiller la curiosité des enfants et des adultes. Le musée propose des expositions ludiques et éducatives sur des thèmes variés, allant des sciences naturelles à la technologie. Les visiteurs peuvent participer à des ateliers pratiques et découvrir de manière amusante et engageante le monde qui les entoure. Les expositions sont régulièrement renouvelées pour offrir des expériences toujours nouvelles et captivantes.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.51982119434193',
                'coordonneesY' => '6.603723832410167',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Parc de Mon Repos',
                'image_url' => 'storage\images\Parc_Repos.jpg',
                'description' => 'Le Parc de Mon Repos, situé au cœur de Lausanne, est un parc historique offrant un cadre paisible pour la promenade et la détente. Le parc est doté de magnifiques jardins, de sculptures et de bâtiments historiques. C\'est un lieu de rencontre apprécié des Lausannois, idéal pour se détendre, lire un livre ou profiter d\'un pique-nique en plein air. Des événements culturels et des concerts en plein air sont régulièrement organisés dans ce cadre enchanteur.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.51885180766259',
                'coordonneesY' => '6.643678468431785',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Musée Cantonal des Beaux Arts',
                'image_url' => 'storage\images\Beaux_Arts.jpg',
                'description' => 'Le Musée Cantonal des Beaux-Arts de Lausanne est une institution culturelle majeure qui abrite une riche collection d\'œuvres d\'art, allant de la peinture et la sculpture aux arts graphiques. Le musée organise régulièrement des expositions temporaires mettant en valeur des artistes suisses et internationaux. Situé dans le quartier des arts, il offre un cadre moderne et élégant pour la découverte de l\'art. Le musée propose également des programmes éducatifs et des visites guidées pour approfondir la compréhension des œuvres exposées.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.51803461834509',
                'coordonneesY' => '6.625447673505602',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Pully Museum',
                'image_url' => 'storage\images\Pully_Musee.jpg',
                'description' => 'Le Musée de Pully, situé dans la banlieue de Lausanne, offre une riche collection d\'art et d\'artéfacts qui retracent l\'histoire locale et la culture de la région. Les expositions comprennent des œuvres d\'art, des sculptures et des objets archéologiques. Le musée organise également des ateliers, des conférences et des événements spéciaux pour enrichir l\'expérience des visiteurs et promouvoir la culture locale.',
                'localite' => 'Pully',
                'coordonneesX' => '46.50918000329301',
                'coordonneesY' => '6.660383726102434',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Parc Bourget',
                'image_url' => 'storage\images\Bourget.png',
                'description' => 'Le Parc Bourget, situé au bord du lac Léman à Lausanne, est un espace de loisirs très apprécié des habitants et des visiteurs. Il offre de vastes espaces verts, des aires de jeux, des terrains de sport et des chemins de promenade le long du lac. Le parc est également un lieu de rencontre pour des événements communautaires, des festivals et des pique-niques en famille. Les visiteurs peuvent profiter de la beauté naturelle du parc tout en participant à diverses activités récréatives.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.51936932355353',
                'coordonneesY' => '6.5875864837738645',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Jardin Botanique de Lausanne',
                'image_url' => 'storage\images\Jardin_Botanique.jpg',
                'description' => 'Le Jardin Botanique de Lausanne est un lieu de découverte et de détente où les visiteurs peuvent explorer une vaste collection de plantes provenant du monde entier. Le jardin offre également des activités éducatives et des événements tout au long de l\'année, tels que des visites guidées, des ateliers de jardinage et des expositions florales. C\'est un lieu idéal pour les amoureux de la nature et ceux qui souhaitent en apprendre davantage sur la botanique.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.51351433891625',
                'coordonneesY' => '6.6226747684316525',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Esplanade de Montbenon',
                'image_url' => 'storage\images\Esplanade.jpg',
                'description' => 'L\'Esplanade de Montbenon est un espace public situé au centre de Lausanne, offrant une vue panoramique sur le lac Léman et les Alpes. C\'est un lieu populaire pour les promenades, les concerts en plein air et les événements culturels. L\'esplanade est entourée de bâtiments historiques et de jardins bien entretenus, ce qui en fait un endroit idéal pour se détendre et profiter de la beauté de la ville.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.52014487387062',
                'coordonneesY' => '6.625975183773872',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'nom' => 'Place de la Palud',
                'image_url' => 'storage\images\Place_Palud.jpg',
                'description' => 'La Place de la Palud est une place historique située au cœur de Lausanne. Elle est connue pour sa fontaine ornée et l\'horloge animée qui raconte l\'histoire de la ville. C\'est un lieu de rencontre animé, entouré de boutiques et de cafés. La place accueille également un marché hebdomadaire où les habitants et les visiteurs peuvent acheter des produits locaux et artisanaux.',
                'localite' => 'Lausanne',
                'coordonneesX' => '46.522014545463406',
                'coordonneesY' => '6.633064954938418',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
