<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CategoryTableSeeder extends Seeder
{
    public function run(): void
    {
        $baseUrl = 'http://www.togoactualite.com/wp-json/wp/v2/categories';
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
                '❌ Impossible de récupérer les catégories depuis WordPress.'
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
            "🔎 Total de pages : {$totalPages}"
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

                $categories = $response->json();

                $fetchedThisPage = count($categories);
                $insertedThisPage = 0;

                foreach ($categories as $categoryData) {
                    $category = Category::updateOrCreate(
                        [
                            'wp_category_id' => $categoryData['id'],
                        ],
                        [
                            'user_id' => $user->id,
                            'wp_category_id' => $categoryData['id'],
                            'name' => $categoryData['name'],
                            'slug' => $categoryData['slug'],
                        ]
                    );

                    if ($category->wasRecentlyCreated) {
                        $insertedThisPage++;
                    }
                }

                $totalFetched += $fetchedThisPage;
                $totalInserted += $insertedThisPage;

                $this->command->info(
                    "📄 Page {$page} : "
                        . "{$fetchedThisPage} récupérées, "
                        . "{$insertedThisPage} créées."
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
                . "{$totalFetched} catégories traitées, "
                . "{$totalInserted} créées."
        );
    }
}
