<?php

namespace Database\Seeders;

use App\Models\TypeFile;
use Illuminate\Database\Seeder;

class FileTypeTableSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => "Images", 'slug_wp' => "image", 'slug' => "images"],
            ['name' => "Vidéos", 'slug_wp' => "video", 'slug' => "videos"],
            ['name' => "Documents", 'slug_wp' => "text", 'slug' => "documents"],
            ['name' => "Applications", 'slug_wp' => "application", 'slug' => "applications"],
            ['name' => "Audios", 'slug_wp' => "audio", 'slug' => "audios"],
        ];

        $totalFetched = count($types);
        $totalInserted = 0;

        foreach ($types as $typeData) {
            $typeFile = TypeFile::updateOrCreate(
                ['slug_wp' => $typeData['slug_wp']], // clé unique
                [
                    'name' => $typeData['name'],
                    'slug' => $typeData['slug'],
                    'date_publish' => now(),
                    'user_id' => 1,
                ]
            );

            if ($typeFile->wasRecentlyCreated) {
                $totalInserted++;
            }

            $this->command->info("Type '{$typeFile->name}' traité : " . ($typeFile->wasRecentlyCreated ? 'nouveau' : 'existant'));
        }

        $this->command->info("✅ Import des types de fichiers terminé : $totalFetched récupérés, $totalInserted insérés en base.");
    }
}
