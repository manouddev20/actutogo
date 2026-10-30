<?php

namespace Database\Seeders;

use App\Models\TypePublication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TypePublicationTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Début du transfert des types de publications...');

        $types = [
            'Annonce',
            'Alerte Infos',
            'Publicité',
            'Article',
            'Événement',
            'Vidéo',
        ];

        $transferred = 0;

        foreach ($types as $name) {
            TypePublication::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'user_id' => 1,
                    'status' => true,
                ]
            );

            $transferred++;

            $this->command->info(
                'Type de publication transféré : ' .
                $transferred .
                ' / ' .
                count($types)
            );
        }

        $this->command->info(
            'Transfert des types de publications terminé : ' .
            $transferred .
            ' types.'
        );
    }
}