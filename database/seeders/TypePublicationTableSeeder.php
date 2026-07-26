<?php

namespace Database\Seeders;

use App\Models\TypePublication;
use Illuminate\Database\Seeder;

class TypePublicationTableSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => "Articles", 'slug' => "articles"],
            ['name' => "Alerte Infos", 'slug' => "alerte-infos"],
            ['name' => "Annonces", 'slug' => "annonces"],
            ['name' => "Vidéos", 'slug' => "videos"],
            ['name' => "Publicités", 'slug' => "publicites"],
            ['name' => "Evénnements", 'slug' => "evenements"],
        ];

        $totalFetched = count($types);
        $totalInserted = 0;

        foreach ($types as $typeData) {
            $type = TypePublication::updateOrCreate(
                ['slug' => $typeData['slug']], // clé unique
                [
                    'name' => $typeData['name'],
                    'date_publish' => now(),
                    'user_id' => 1,
                ]
            );

            if ($type->wasRecentlyCreated) {
                $totalInserted++;
            }

            $this->command->info("Type de publication '{$type->name}' traité : " . ($type->wasRecentlyCreated ? 'nouveau' : 'existant'));
        }

        $this->command->info("✅ Import des types de publication terminé : $totalFetched récupérés, $totalInserted insérés en base.");
    }
}
