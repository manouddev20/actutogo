<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuthorTableSeeder extends Seeder
{
    public function run()
    {
        $datas = [
            [
                'nom' => 'DENANYOH',
                'prenoms' => 'Alexandre',
                'nom_complet' => 'DENANYOH Alexandre',
                'email' => 'nonojack@yahoo.fr',
                'telephone' => '+33 6 27 38 75 14',
                'authorName' => 'Togo infos',
                'slug' => Str::slug('Togo infos'),
                'wp_author_id' => 1,
                'date_publish' => now(),
                'description' => "Nous tenons à rappeler aux visiteurs du site que
                    sans partenariat avec togoactualite.com, la reprise des articles même partielle est strictement interdite.
                    Tout contrevenant s'expose à de graves poursuites."
            ],
            [
                'nom' => 'MIKANDO',
                'prenoms' => 'Eric',
                'nom_complet' => 'MIKANDO Eric',
                'email' => 'ericmakondo@gmail.com',
                'telephone' => '+228 91 91 91 91',
                'authorName' => 'delomepub',
                'slug' => Str::slug('delomepub'),
                'wp_author_id' => 10,
                'date_publish' => now(),
                'description' => "Nous tenons à rappeler aux visiteurs du site que
                    sans partenariat avec togoactualite.com, la reprise des articles même partielle est strictement interdite.
                    Tout contrevenant s'expose à de graves poursuites."
            ],
        ];

        $totalFetched = count($datas);
        $totalInserted = 0;

        foreach ($datas as $authorData) {
            $author = Author::updateOrCreate(
                ['slug' => $authorData['slug']], // condition d’unicité
                $authorData
            );

            if ($author->wasRecentlyCreated) {
                $totalInserted++;
            }

            $this->command->info("Auteur '{$author->nom_complet}' traité : " . ($author->wasRecentlyCreated ? 'nouveau' : 'existant'));
        }

        $this->command->info("✅ Import des auteurs terminé : $totalFetched récupérés, $totalInserted insérés en base.");
    }
}
