<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Category;
use App\Models\MediaFile;
use App\Models\Publication;
use App\Models\Tag;
use App\Models\TypePublication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PublicationTableSeeder extends Seeder
{
    public function run(): void
    {
        $perPage = 100;

        /*
        |--------------------------------------------------------------------------
        | Vérification de l'utilisateur responsable de l'import
        |--------------------------------------------------------------------------
        */

        $user = User::query()->first();

        if (!$user) {
            $this->command->error(
                '❌ Aucun utilisateur trouvé. '
                . 'Veuillez exécuter UserTableSeeder avant PublicationTableSeeder.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Vérification du type de publication
        |--------------------------------------------------------------------------
        */

        $typePublication = TypePublication::query()
            ->where('slug', 'articles')
            ->first();

        if (!$typePublication) {
            $this->command->error(
                '❌ Le type de publication "articles" est introuvable.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Récupération de la première page WordPress
        |--------------------------------------------------------------------------
        */

        try {
            $response = Http::timeout(60)->get(
                'http://www.togoactualite.com/wp-json/wp/v2/posts',
                [
                    'per_page' => $perPage,
                    'page' => 1,
                ]
            );
        } catch (\Throwable $exception) {
            $this->command->error(
                '❌ Erreur de connexion à WordPress : '
                . $exception->getMessage()
            );

            return;
        }

        if (!$response->successful()) {
            $this->command->error(
                '❌ Impossible de récupérer les publications depuis WordPress.'
            );

            return;
        }

        $totalPages = (int) $response->header(
            'x-wp-totalpages',
            1
        );

        $totalPosts = (int) $response->header(
            'x-wp-total',
            0
        );

        $this->command->info(
            "📰 {$totalPosts} publication(s) trouvée(s) sur "
            . "{$totalPages} page(s)."
        );

        $grandTotalFetched = 0;
        $grandTotalInserted = 0;
        $grandTotalUpdated = 0;

        /*
        |--------------------------------------------------------------------------
        | Boucle sur toutes les pages WordPress
        |--------------------------------------------------------------------------
        */

        for ($page = 1; $page <= $totalPages; $page++) {

            try {
                $postsResponse = Http::timeout(60)->get(
                    'http://www.togoactualite.com/wp-json/wp/v2/posts',
                    [
                        'per_page' => $perPage,
                        'page' => $page,
                    ]
                );
            } catch (\Throwable $exception) {
                $this->command->warn(
                    "⚠️ Erreur lors de la récupération de la page {$page} : "
                    . $exception->getMessage()
                );

                continue;
            }

            if (!$postsResponse->successful()) {
                $this->command->warn(
                    "⚠️ Impossible de récupérer la page {$page}."
                );

                continue;
            }

            $posts = $postsResponse->json();

            $fetchedThisPage = count($posts);
            $insertedThisPage = 0;
            $updatedThisPage = 0;

            /*
            |--------------------------------------------------------------------------
            | Boucle sur les publications de la page
            |--------------------------------------------------------------------------
            */

            foreach ($posts as $post) {

                /*
                |--------------------------------------------------------------------------
                | Vérification de l'identifiant WordPress
                |--------------------------------------------------------------------------
                */

                if (empty($post['id'])) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Auteur
                |--------------------------------------------------------------------------
                */

                $author = null;

                if (!empty($post['author'])) {
                    $author = Author::query()
                        ->where(
                            'wp_author_id',
                            $post['author']
                        )
                        ->first();
                }

                /*
                |--------------------------------------------------------------------------
                | Dates
                |--------------------------------------------------------------------------
                */

                $datePublish = !empty($post['date'])
                    ? Carbon::parse($post['date'])
                    : now();

                $dateModified = !empty($post['modified'])
                    ? Carbon::parse($post['modified'])
                    : $datePublish;

                /*
                |--------------------------------------------------------------------------
                | Titre
                |--------------------------------------------------------------------------
                */

                $title = !empty($post['title']['rendered'])
                    ? trim(
                        strip_tags(
                            html_entity_decode(
                                $post['title']['rendered'],
                                ENT_QUOTES | ENT_HTML5,
                                'UTF-8'
                            )
                        )
                    )
                    : 'Sans titre';

                /*
                |--------------------------------------------------------------------------
                | Contenu
                |--------------------------------------------------------------------------
                */

                $content = !empty($post['content']['rendered'])
                    ? $post['content']['rendered']
                    : null;

                /*
                |--------------------------------------------------------------------------
                | Extrait
                |--------------------------------------------------------------------------
                */

                $excerpt = !empty($post['excerpt']['rendered'])
                    ? $post['excerpt']['rendered']
                    : null;

                $excerptText = $excerpt
                    ? trim(
                        strip_tags(
                            html_entity_decode(
                                $excerpt,
                                ENT_QUOTES | ENT_HTML5,
                                'UTF-8'
                            )
                        )
                    )
                    : null;

                /*
                |--------------------------------------------------------------------------
                | Slug
                |--------------------------------------------------------------------------
                */

                $slug = !empty($post['slug'])
                    ? $post['slug']
                    : Str::slug($title);

                /*
                |--------------------------------------------------------------------------
                | Média de couverture
                |--------------------------------------------------------------------------
                */

                $coverMediaFile = null;

                if (!empty($post['featured_media'])) {

                    $coverMediaFile = MediaFile::query()
                        ->where(
                            'wp_file_id',
                            $post['featured_media']
                        )
                        ->first();
                }

                /*
                |--------------------------------------------------------------------------
                | Création ou mise à jour de la publication
                |--------------------------------------------------------------------------
                */

                $publication = Publication::withTrashed()
                    ->where(
                        'wp_publication_id',
                        $post['id']
                    )
                    ->first();

                $publicationData = [
                    'user_id' => $user->id,

                    'author_id' => $author?->id,

                    'type_publication_id' => $typePublication->id,

                    'cover_media_file_id' => $coverMediaFile?->id,

                    'wp_publication_id' => $post['id'],

                    'title' => $title,

                    'slug' => $slug,

                    'title_truncate' => Str::words(
                        $title,
                        10,
                        '...'
                    ),

                    'content' => $content,

                    'truncate_content' => $excerptText
                        ? Str::words(
                            $excerptText,
                            20,
                            '...'
                        )
                        : null,

                    'truncate_content_max' => $excerptText,

                    'views_count' => random_int(1500, 3000),

                    'likes_count' => random_int(700, 1800),

                    'shares_count' => random_int(100, 540),

                    'status' => (
                        ($post['status'] ?? null) === 'publish'
                    ),

                    'comment_status' => (
                        ($post['comment_status'] ?? null) === 'open'
                    ),

                    'date_publish' => $datePublish,

                    'date_modified' => $dateModified,

                    'source' => 'Togoactualité',

                    'wp_link' => $post['link'] ?? null,
                ];

                /*
                |--------------------------------------------------------------------------
                | Création
                |--------------------------------------------------------------------------
                */

                if (!$publication) {

                    $publication = Publication::create(
                        $publicationData
                    );

                    $insertedThisPage++;
                } else {

                    /*
                    | Restauration si la publication
                    | avait été supprimée avec SoftDeletes.
                    */

                    if ($publication->trashed()) {
                        $publication->restore();
                    }

                    $publication->update(
                        $publicationData
                    );

                    $updatedThisPage++;
                }

                /*
                |--------------------------------------------------------------------------
                | Catégories
                |--------------------------------------------------------------------------
                */

                $categoryIds = [];

                if (
                    !empty($post['categories'])
                    && is_array($post['categories'])
                ) {

                    foreach (
                        $post['categories']
                        as $wpCategoryId
                    ) {

                        $category = Category::query()
                            ->where(
                                'wp_category_id',
                                $wpCategoryId
                            )
                            ->first();

                        if ($category) {
                            $categoryIds[] = $category->id;
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Synchronisation des catégories
                |--------------------------------------------------------------------------
                */

                $publication->categories()->sync(
                    array_unique($categoryIds)
                );

                /*
                |--------------------------------------------------------------------------
                | Tags
                |--------------------------------------------------------------------------
                */

                $tagIds = [];

                if (
                    !empty($post['tags'])
                    && is_array($post['tags'])
                ) {

                    foreach (
                        $post['tags']
                        as $wpTagId
                    ) {

                        $tag = Tag::query()
                            ->where(
                                'wp_tag_id',
                                $wpTagId
                            )
                            ->first();

                        if ($tag) {
                            $tagIds[] = $tag->id;
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Synchronisation des tags
                |--------------------------------------------------------------------------
                */

                $publication->tags()->sync(
                    array_unique($tagIds)
                );

                /*
                |--------------------------------------------------------------------------
                | Médias de la publication
                |--------------------------------------------------------------------------
                |
                | Pour le moment, on attache au moins
                | le média de couverture.
                |
                | Si plus tard tu récupères les autres
                | images depuis le contenu WordPress,
                | elles pourront également être ajoutées ici.
                |--------------------------------------------------------------------------
                */

                $mediaFileIds = [];

                if ($coverMediaFile) {
                    $mediaFileIds[] =
                        $coverMediaFile->id;
                }

                /*
                |--------------------------------------------------------------------------
                | Synchronisation des médias
                |--------------------------------------------------------------------------
                */

                $publication->mediaFiles()->sync(
                    array_unique($mediaFileIds)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Compteurs de la page
            |--------------------------------------------------------------------------
            */

            $grandTotalFetched += $fetchedThisPage;
            $grandTotalInserted += $insertedThisPage;
            $grandTotalUpdated += $updatedThisPage;

            $this->command->info(
                "📄 Page {$page}/{$totalPages} : "
                . "{$fetchedThisPage} récupérées, "
                . "{$insertedThisPage} créées, "
                . "{$updatedThisPage} mises à jour."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Mise à jour des compteurs
        |--------------------------------------------------------------------------
        */

        $this->updatePublicationCounters();

        /*
        |--------------------------------------------------------------------------
        | Résultat final
        |--------------------------------------------------------------------------
        */

        $this->command->info(
            '🎯 Import des publications terminé : '
            . "{$grandTotalFetched} récupérées, "
            . "{$grandTotalInserted} créées, "
            . "{$grandTotalUpdated} mises à jour."
        );
    }

    /**
     * Met à jour les compteurs des différentes tables.
     */
    private function updatePublicationCounters(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Auteurs
        |--------------------------------------------------------------------------
        */

        Author::query()->each(function (Author $author) {

            $author->update([
                'count_publications' =>
                    $author->publications()->count(),
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Types de publication
        |--------------------------------------------------------------------------
        */

        TypePublication::query()->each(
            function (TypePublication $typePublication) {

                $typePublication->update([
                    'count_publications' =>
                        $typePublication
                            ->publications()
                            ->count(),
                ]);
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Catégories
        |--------------------------------------------------------------------------
        */

        Category::query()->each(
            function (Category $category) {

                $category->update([
                    'count_publications' =>
                        $category
                            ->publications()
                            ->count(),
                ]);
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Tags
        |--------------------------------------------------------------------------
        */

        Tag::query()->each(function (Tag $tag) {

            $tag->update([
                'count_publications' =>
                    $tag->publications()->count(),
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Fichiers médias
        |--------------------------------------------------------------------------
        */

        MediaFile::query()->each(
            function (MediaFile $mediaFile) {

                $mediaFile->update([
                    'count_publications' =>
                        $mediaFile
                            ->publications()
                            ->count(),
                ]);
            }
        );

        $this->command->info(
            '🔄 Compteurs de publications mis à jour.'
        );
    }
}