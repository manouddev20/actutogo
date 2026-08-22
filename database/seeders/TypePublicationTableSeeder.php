<?php

namespace Database\Seeders;

use App\Models\TypePublication;
use App\Models\User;
use Illuminate\Database\Seeder;

class TypePublicationTableSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Récupération de l'utilisateur
         * qui sera enregistré comme créateur
         * des types de publication.
         */
        $user = User::query()->first();

        if (!$user) {
            $this->command->error(
                '❌ Aucun utilisateur trouvé. '
                . 'Exécutez d\'abord UserTableSeeder.'
            );

            return;
        }

        $types = [
            [
                'name' => 'Articles',
                'slug' => 'articles',
            ],
            [
                'name' => 'Alerte Infos',
                'slug' => 'alerte-infos',
            ],
            [
                'name' => 'Annonces',
                'slug' => 'annonces',
            ],
            [
                'name' => 'Vidéos',
                'slug' => 'videos',
            ],
            [
                'name' => 'Publicités',
                'slug' => 'publicites',
            ],
            [
                'name' => 'Événements',
                'slug' => 'evenements',
            ],
        ];

        $total = count($types);
        $inserted = 0;

        foreach ($types as $typeData) {

            $type = TypePublication::updateOrCreate(
                [
                    /*
                     * Clé unique de recherche.
                     */
                    'slug' => $typeData['slug'],
                ],

                [
                    'name' => $typeData['name'],
                    'slug' => $typeData['slug'],

                    /*
                     * Utilisateur ayant créé
                     * le type de publication.
                     */
                    'user_id' => $user->id,
                ]
            );

            if ($type->wasRecentlyCreated) {
                $inserted++;
            }

            $this->command->info(
                "Type de publication '{$type->name}' : "
                . (
                    $type->wasRecentlyCreated
                        ? 'créé'
                        : 'déjà existant / mis à jour'
                )
            );
        }

        $this->command->info(
            "✅ Import terminé : "
            . "{$total} types de publication traités, "
            . "{$inserted} créés."
        );
    }
}