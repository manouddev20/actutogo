<?php

namespace Database\Seeders;

use App\Models\NewsLetter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsLetterTableSeeder extends Seeder
{
    public function run(): void
    {
        $emails = [
            'manouadjanor@gmail.com',
            'nonojack@yahoo.fr',
        ];

        $total = count($emails);
        $inserted = 0;

        foreach ($emails as $email) {
            $newsletter = NewsLetter::updateOrCreate(
                [
                    'email' => $email,
                ],
                [
                    'email' => $email,
                    'slug' => Str::slug($email),
                    'status' => 1,
                ]
            );

            if ($newsletter->wasRecentlyCreated) {
                $inserted++;
            }

            $this->command->info(
                "Newsletter '{$newsletter->email}' : "
                . ($newsletter->wasRecentlyCreated ? 'créée' : 'existante')
            );
        }

        $this->command->info(
            "✅ Import terminé : {$total} newsletters traitées, "
            . "{$inserted} créées."
        );
    }
}