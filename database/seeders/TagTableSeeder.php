<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class TagTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('==============================================');
        $this->command->info('DÉBUT DU TRANSFERT DES TAGS WORDPRESS');
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

        $baseUrl = 'https://togoactualite.com/wp-json/wp/v2/tags';
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

        $totalTags = (int) $response->header('X-WP-Total');
        $totalPages = (int) $response->header('X-WP-TotalPages');

        if ($totalTags === 0) {
            $this->command->warn('Aucun tag trouvé dans WordPress.');
            return;
        }

        if ($totalPages === 0) {
            $totalPages = (int) ceil($totalTags / $perPage);
        }

        $this->command->info(
            'Nombre total de tags : ' . $totalTags
        );

        $this->command->info(
            'Nombre total de pages : ' . $totalPages
        );

        $this->command->info(
            'Taille de chaque page : jusqu’à ' . $perPage . ' tags'
        );

        $transferred = 0;
        $created = 0;
        $updated = 0;

        for ($page = 1; $page <= $totalPages; $page++) {
            if ($page === 1) {
                $tags = $response->json();
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

                $tags = $pageResponse->json();
            }

            $pageTransferred = 0;

            foreach ($tags as $tagData) {
                if (
                    !isset($tagData['id']) ||
                    !isset($tagData['name']) ||
                    !isset($tagData['slug'])
                ) {
                    continue;
                }

                $wpTagId = (int) $tagData['id'];

                $tag = Tag::withTrashed()->where(
                    'wp_tag_id',
                    $wpTagId
                )->first();

                $data = [
                    'user_id' => $user->id,
                    'name' => trim(strip_tags($tagData['name'])),
                    'slug' => Str::slug($tagData['slug']),
                    'status' => true,
                    'wp_tag_id' => $wpTagId,
                ];

                if ($tag) {
                    $tag->update($data);

                    if ($tag->trashed()) {
                        $tag->restore();
                    }

                    $updated++;
                } else {
                    Tag::create($data);
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
                ' | Tags récupérés : ' .
                $pageTransferred .
                ' | Progression : ' .
                $transferred .
                ' / ' .
                $totalTags
            );
        }

        $this->command->info('----------------------------------------------');
        $this->command->info(
            'Tags créés : ' . $created
        );
        $this->command->info(
            'Tags mis à jour : ' . $updated
        );
        $this->command->info(
            'Tags transférés : ' . $transferred . ' / ' . $totalTags
        );
        $this->command->info(
            'Nombre de pages traitées : ' . $totalPages
        );
        $this->command->info('TRANSFERT DES TAGS TERMINÉ.');
        $this->command->info('==============================================');
    }
}