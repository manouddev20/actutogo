<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuthorTableSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();

        if (!$user) {
            $this->command->error(
                "❌ Aucun utilisateur trouvé. "
                    . "Créez d'abord un utilisateur avant d'exécuter ce seeder."
            );

            return;
        }

        $authors = [
            [
                'first_name' => 'Alexandre',
                'last_name' => 'DENANYOH',
                'phone' => '+33 6 27 38 75 14',
                'email' => 'nonojack@yahoo.fr',
                'name' => 'Togo infos',
                'slug' => Str::slug('Togo infos'),
                'wp_author_id' => 1,
                'description' => "Nous tenons à rappeler aux visiteurs du site que
sans partenariat avec togoactualite.com, la reprise des articles même partielle
est strictement interdite. Tout contrevenant s'expose à de graves poursuites.",
            ],
            [
                'first_name' => 'Eric',
                'last_name' => 'MIKANDO',
                'phone' => '+33 6 27 38 75 14',
                'email' => 'mikando@example.com',
                'name' => 'dutogoactu',
                'slug' => Str::slug('dutogoactu'),
                'wp_author_id' => 10,
                'description' => "Nous tenons à rappeler aux visiteurs du site que
sans partenariat avec togoactualite.com, la reprise des articles même partielle
est strictement interdite. Tout contrevenant s'expose à de graves poursuites.",
            ],
        ];

        $total = count($authors);
        $inserted = 0;

        foreach ($authors as $authorData) {
            $slug = Str::slug($authorData['name']);

            $author = Author::updateOrCreate(
                [
                    'wp_author_id' => $authorData['wp_author_id'] ?? null,
                ],
                [
                    'user_id' => $user->id,
                    'wp_author_id' => $authorData['wp_author_id'] ?? null,
                    'first_name' => $authorData['first_name'],
                    'last_name' => $authorData['last_name'],
                    'slug' => $authorData['slug'] ?? $slug,
                    'description' => $authorData['description'],
                    'phone' => $authorData['phone'] ?? null,
                    'email' => $authorData['email'] ?? null,
                ]
            );

            if ($author->wasRecentlyCreated) {
                $inserted++;
            }

            $this->command->info(
                "Auteur '{$author->first_name} {$author->last_name}' : "
                    . ($author->wasRecentlyCreated ? 'créé' : 'mis à jour')
            );
        }

        $this->command->info(
            "✅ Import terminé : {$total} auteurs traités, {$inserted} créés."
        );
    }
}
