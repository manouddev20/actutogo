<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CategoryTableSeeder extends Seeder
{
    public function run()
    {
        $baseUrl = "http://www.togoactualite.com/wp-json/wp/v2/categories";
        $perPage = 100;
        $batchSize = 5;

        try {
            $response = Http::get("$baseUrl?per_page=$perPage&page=1");
        } catch (\Exception $e) {
            $this->command->error("Erreur de connexion à l'API : " . $e->getMessage());
            return;
        }

        if (!$response->successful()) {
            $this->command->error('❌ Impossible de récupérer les catégories depuis WordPress.');
            return;
        }

        $totalPages = intval($response->header('x-wp-totalpages', 1));
        $this->command->info("🔎 Total pages à traiter : $totalPages");

        $totalFetched = 0;
        $totalInserted = 0;

        $pages = range(1, $totalPages);

        foreach (array_chunk($pages, $batchSize) as $batch) {
            foreach ($batch as $page) {
                try {
                    $response = Http::get("$baseUrl?per_page=$perPage&page=$page");

                    if (!$response->successful()) {
                        $this->command->warn("⚠️ Échec récupération page $page (status: " . $response->status() . ")");
                        continue;
                    }

                    $categories = $response->json();
                    $fetchedThisPage = count($categories);
                    $insertedThisPage = 0;

                    foreach ($categories as $categoryData) {
                        $category = Category::updateOrCreate(
                            ['wp_category_id' => intval($categoryData['id'])],
                            [
                                'name' => $categoryData['name'],
                                'slug' => $categoryData['slug'],
                                'count_publications' => 0,
                                'date_publish' => now(),
                                'user_id' => 1
                            ]
                        );

                        if ($category->wasRecentlyCreated) {
                            $insertedThisPage++;
                        }
                    }

                    $totalFetched += $fetchedThisPage;
                    $totalInserted += $insertedThisPage;

                    $this->command->info("📄 Page $page traitée : $fetchedThisPage récupérées, $insertedThisPage insérées. Total insérées : $totalInserted");
                } catch (\Exception $e) {
                    $this->command->warn("⚠️ Erreur page $page : " . $e->getMessage());
                }
            }
        }

        $this->command->info("✅ Import des catégories terminé : $totalFetched récupérées au total, $totalInserted insérées en base.");
    }
}
