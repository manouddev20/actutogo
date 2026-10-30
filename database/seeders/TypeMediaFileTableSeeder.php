<?php

namespace Database\Seeders;

use App\Models\TypeMediaFile;
use Illuminate\Database\Seeder;

class TypeMediaFileTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('==============================================');
        $this->command->info('DÉBUT DU TRANSFERT DES TYPES DE FICHIERS');
        $this->command->info('==============================================');

        $types = [
            [
                'name' => 'Image',
                'slug' => 'image',
                'mime_type' => 'image/*',
                'status' => true,
            ],
            [
                'name' => 'Vidéo',
                'slug' => 'video',
                'mime_type' => 'video/*',
                'status' => true,
            ],
            [
                'name' => 'Audio',
                'slug' => 'audio',
                'mime_type' => 'audio/*',
                'status' => true,
            ],
            [
                'name' => 'Texte',
                'slug' => 'text',
                'mime_type' => 'text/*',
                'status' => true,
            ],
            [
                'name' => 'Application',
                'slug' => 'application',
                'mime_type' => 'application/*',
                'status' => true,
            ],
        ];

        $transferred = 0;

        foreach ($types as $type) {
            TypeMediaFile::updateOrCreate(
                ['slug' => $type['slug']],
                [
                    'name' => $type['name'],
                    'mime_type' => $type['mime_type'],
                    'status' => $type['status'],
                ]
            );

            $transferred++;

            $this->command->info(
                'Type de fichier transféré : ' .
                $transferred .
                ' / ' .
                count($types)
            );
        }

        $this->command->info('----------------------------------------------');
        $this->command->info(
            'Types de fichiers transférés : ' .
            $transferred
        );
        $this->command->info('TRANSFERT DES TYPES DE FICHIERS TERMINÉ.');
        $this->command->info('==============================================');
    }
}