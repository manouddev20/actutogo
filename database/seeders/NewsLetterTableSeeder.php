<?php

namespace Database\Seeders;

use App\Models\NewsLetter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsLetterTableSeeder extends Seeder
{
    public function run()
    {
        $newsLetters = [
            'manouadjanor@gmail.com',
            'nonojack@yahoo.fr'
        ];

        $totalFetched = count($newsLetters);
        $totalInserted = 0;

        foreach ($newsLetters as $email) {
            $newsletter = NewsLetter::updateOrCreate(
                ['email' => $email], // clé unique
                [
                    'slug' => Str::slug($email),
                    'status' => 1,
                    'date_publish' => now(),
                ]
            );

            if ($newsletter->wasRecentlyCreated) {
                $totalInserted++;
            }

            $this->command->info("NewsLetter '{$newsletter->email}' traitée : " . ($newsletter->wasRecentlyCreated ? 'nouveau' : 'existant'));
        }

        $this->command->info("✅ Import des newsletters terminé : $totalFetched récupérées, $totalInserted insérées en base.");
    }
}
