<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class CategoryTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('==============================================');
        $this->command->info('DÉBUT DU TRANSFERT DES CATÉGORIES WORDPRESS');
        $this->command->info('==============================================');

        $user = User::whereHas('role', function ($query) {
            $query->where('slug', 'administrateur');
        })->first();

        if (!$user) {
            $this->command->error(
                'Aucun utilisateur administrateur trouvé. Le transfert est annulé.'
            );

            return;
        }

        $baseUrl = 'https://togoactualite.com/wp-json/wp/v2/categories';
        $perPage = 100;

        $this->command->info('Connexion à l’API WordPress...');
        $this->command->info('URL : ' . $baseUrl);
        $this->command->info('Nombre demandé par page : ' . $perPage);

        $response = Http::acceptJson()
            ->timeout(120)
            ->retry(3, 1000)
            ->get($baseUrl, [
                'per_page' => $perPage,
                'page' => 1,
            ]);

        if (!$response->successful()) {
            throw new RuntimeException(
                'Erreur API WordPress : HTTP ' . $response->status()
            );
        }

        $totalCategories = (int) $response->header('X-WP-Total');
        $totalPages = (int) $response->header('X-WP-TotalPages');

        if ($totalCategories === 0) {
            $this->command->warn('Aucune catégorie trouvée dans WordPress.');
            return;
        }

        if ($totalPages === 0) {
            $totalPages = (int) ceil($totalCategories / $perPage);
        }

        $this->command->info(
            'Nombre total de catégories : ' . $totalCategories
        );

        $this->command->info(
            'Nombre total de pages : ' . $totalPages
        );

        $this->command->info(
            'Taille de chaque page : jusqu’à ' . $perPage . ' catégories'
        );

        $transferred = 0;
        $created = 0;
        $updated = 0;

        for ($page = 1; $page <= $totalPages; $page++) {
            if ($page === 1) {
                $categories = $response->json();
            } else {
                $pageResponse = Http::acceptJson()
                    ->timeout(120)
                    ->retry(3, 1000)
                    ->get($baseUrl, [
                        'per_page' => $perPage,
                        'page' => $page,
                    ]);

                if (!$pageResponse->successful()) {
                    $this->command->error(
                        'Erreur lors de la récupération de la page ' .
                        $page .
                        ' : HTTP ' .
                        $pageResponse->status()
                    );

                    continue;
                }

                $categories = $pageResponse->json();
            }

            $pageTransferred = 0;

            foreach ($categories as $categoryData) {
                if (
                    !isset($categoryData['id']) ||
                    !isset($categoryData['name']) ||
                    !isset($categoryData['slug'])
                ) {
                    continue;
                }

                $wpCategoryId = (int) $categoryData['id'];

                $category = Category::withTrashed()->where(
                    'wp_category_id',
                    $wpCategoryId
                )->first();

                $data = [
                    'user_id' => $user->id,
                    'name' => trim(strip_tags($categoryData['name'])),
                    'slug' => Str::slug($categoryData['slug']),
                    'status' => true,
                    'wp_category_id' => $wpCategoryId,
                ];

                if ($category) {
                    $category->update($data);

                    if ($category->trashed()) {
                        $category->restore();
                    }

                    $updated++;
                } else {
                    Category::create($data);
                    $created++;
                }

                $transferred++;
                $pageTransferred++;
            }

            $this->command->info(
                'Page ' .
                $page .
                ' / ' .
                $totalPages .
                ' | Catégories récupérées : ' .
                $pageTransferred .
                ' | Progression : ' .
                $transferred .
                ' / ' .
                $totalCategories
            );
        }

        $this->command->info('----------------------------------------------');
        $this->command->info(
            'Catégories créées : ' . $created
        );
        $this->command->info(
            'Catégories mises à jour : ' . $updated
        );
        $this->command->info(
            'Catégories transférées : ' . $transferred . ' / ' . $totalCategories
        );
        $this->command->info(
            'Nombre de pages traitées : ' . $totalPages
        );
        $this->command->info('TRANSFERT DES CATÉGORIES TERMINÉ.');
        $this->command->info('==============================================');
    }
}