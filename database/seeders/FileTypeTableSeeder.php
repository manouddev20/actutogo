<?php

namespace Database\Seeders;

use App\Models\FileType;
use App\Models\User;
use Illuminate\Database\Seeder;

class FileTypeTableSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Récupération de l'utilisateur
         * qui sera enregistré comme créateur
         * des types de fichiers.
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
                'name' => 'Images',
                'slug_wp' => 'image',
                'slug' => 'images',
            ],
            [
                'name' => 'Vidéos',
                'slug_wp' => 'video',
                'slug' => 'videos',
            ],
            [
                'name' => 'Documents',
                'slug_wp' => 'text',
                'slug' => 'documents',
            ],
            [
                'name' => 'Applications',
                'slug_wp' => 'application',
                'slug' => 'applications',
            ],
            [
                'name' => 'Audios',
                'slug_wp' => 'audio',
                'slug' => 'audios',
            ],
        ];

        $total = count($types);
        $inserted = 0;

        foreach ($types as $typeData) {

            $typeFile = FileType::updateOrCreate(
                [
                    'slug_wp' => $typeData['slug_wp'],
                ],
                [
                    'name' => $typeData['name'],
                    'slug_wp' => $typeData['slug_wp'],
                    'slug' => $typeData['slug'],
                    'user_id' => $user->id,
                ]
            );

            if ($typeFile->wasRecentlyCreated) {
                $inserted++;
            }

            $this->command->info(
                "Type de fichier '{$typeFile->name}' : "
                    . (
                        $typeFile->wasRecentlyCreated
                        ? 'créé'
                        : 'déjà existant / mis à jour'
                    )
            );
        }

        $this->command->info(
            "✅ Import terminé : "
                . "{$total} types de fichiers traités, "
                . "{$inserted} créés."
        );
    }
}
