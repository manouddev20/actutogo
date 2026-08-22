<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TagsTableSeeder extends Seeder
{
    public function run(): void
    {
        $baseUrl = 'http://www.togoactualite.com/wp-json/wp/v2/tags';
        $perPage = 100;

        $user = User::query()->first();

        if (!$user) {
            $this->command->error(
                "❌ Aucun utilisateur trouvé. "
                    . "Créez d'abord un utilisateur."
            );

            return;
        }

        try {
            $response = Http::timeout(30)
                ->get($baseUrl, [
                    'per_page' => $perPage,
                    'page' => 1,
                ]);
        } catch (\Throwable $exception) {
            $this->command->error(
                "❌ Erreur de connexion à l'API : "
                    . $exception->getMessage()
            );

            return;
        }

        if (!$response->successful()) {
            $this->command->error(
                "❌ Impossible de récupérer les tags depuis WordPress."
            );

            return;
        }

        $totalPages = (int) $response->header(
            'x-wp-totalpages',
            1
        );

        $totalFetched = 0;
        $totalInserted = 0;

        $this->command->info(
            "🔎 Total de pages de tags : {$totalPages}"
        );

        for ($page = 1; $page <= $totalPages; $page++) {
            try {
                $response = Http::timeout(30)
                    ->get($baseUrl, [
                        'per_page' => $perPage,
                        'page' => $page,
                    ]);

                if (!$response->successful()) {
                    $this->command->warn(
                        "⚠️ Échec de récupération de la page {$page}."
                    );

                    continue;
                }

                $tags = $response->json();

                $fetchedThisPage = count($tags);
                $insertedThisPage = 0;

                foreach ($tags as $tagData) {
                    $name = trim(
                        html_entity_decode(
                            $tagData['name'] ?? '',
                            ENT_QUOTES | ENT_HTML5,
                            'UTF-8'
                        )
                    );

                    $originalSlug = !empty($tagData['slug'])
                        ? Str::slug($tagData['slug'])
                        : Str::slug($name);

                    $slug = $originalSlug;

                    $counter = 1;

                    while (
                        Tag::query()
                        ->where('slug', $slug)
                        ->where('wp_tag_id', '!=', $tagData['id'])
                        ->exists()
                    ) {
                        $slug = $originalSlug . '-' . $counter;
                        $counter++;
                    }

                    $tag = Tag::updateOrCreate(
                        [
                            'wp_tag_id' => $tagData['id'],
                        ],
                        [
                            'user_id' => $user->id,
                            'name' => $name,
                            'slug' => $slug,
                        ]
                    );

                    if ($tag->wasRecentlyCreated) {
                        $insertedThisPage++;
                    }
                }

                $totalFetched += $fetchedThisPage;
                $totalInserted += $insertedThisPage;

                $this->command->info(
                    "📄 Page {$page} : "
                        . "{$fetchedThisPage} récupérés, "
                        . "{$insertedThisPage} créés."
                );
            } catch (\Throwable $exception) {
                $this->command->warn(
                    "⚠️ Erreur page {$page} : "
                        . $exception->getMessage()
                );
            }
        }

        $this->command->info(
            "✅ Import terminé : "
                . "{$totalFetched} tags traités, "
                . "{$totalInserted} créés."
        );
    }
}
