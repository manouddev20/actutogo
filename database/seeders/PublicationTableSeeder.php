<?php

namespace Database\Seeders;

use App\Models\{
    Publication, PublicationFile, PublicationTag,
    Author, Category, File, InfosMonthYear, InfosMonthYearPublication, Tag, TypePublication
};
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PublicationTableSeeder extends Seeder
{
    public function run(): void
    {
        $perPage = 100;
        $response = Http::get("http://www.togoactualite.com/wp-json/wp/v2/posts?per_page=$perPage");

        if (!$response->successful()) {
            $this->command->error("Impossible de récupérer les publications depuis WordPress.");
            return;
        }

        $totalPages = intval($response->header('x-wp-totalpages', 1));
        $this->command->info("Total pages de publications à traiter : $totalPages");

        for ($i = 1; $i <= $totalPages; $i++) {
            $posts = Http::get("http://www.togoactualite.com/wp-json/wp/v2/posts?page=$i&per_page=$perPage")->json();
            $createdCount = 0;

            foreach ($posts as $value) {
                $author = Author::where('wp_author_id', $value['author'])->first();
                $typePublication = TypePublication::where('slug', 'articles')->first();

                if (!$author || !$typePublication) continue;

                // Incrément des compteurs
                $author->increment('count_publications');
                $typePublication->increment('count_publications');

                // Gestion de l'image de couverture
                $imageCoverUrl = null;
                $fileId = null;
                if (isset($value['yoast_head_json']['schema']['@graph'][0]['thumbnailUrl'])) {
                    $url = str_replace(
                        'https://togoactualite.com/wp-content/uploads',
                        'http://togoactualite.com/wp-content/uploads',
                        $value['yoast_head_json']['schema']['@graph'][0]['thumbnailUrl']
                    );
                    $imageCoverUrl = $url;

                    $file = File::firstOrCreate(
                        ['file_url' => $url],
                        [
                            'date_name' => Carbon::parse($value['date'])->format('F Y'),
                            'date_publish' => $value['date'],
                            'type_file_id' => 1,
                            'user_id' => 1,
                        ]
                    );
                    $file->increment('count_publications');
                    $fileId = $file->id;
                }

                // Récupération des catégories
                $categories = collect();
                foreach ($value['categories'] as $wpCategoryId) {
                    $category = Category::where('wp_category_id', $wpCategoryId)->first();
                    if ($category) {
                        $category->increment('count_publications');
                        $categories->push($category);
                    }
                }

                // Récupération des tags
                $tags = collect();
                foreach ($value['tags'] as $wpTagId) {
                    $tag = Tag::where('wp_tag_id', $wpTagId)->first();
                    if ($tag) {
                        $tag->increment('count_publications');
                        $tags->push($tag);
                    }
                }

                // Date name pour InfosMonthYearPublication
                $date = Carbon::parse($value['date']);
                $month = InfosMonthYear::where('month_id', $date->format('m'))->first();
                $dateName = $month ? $month->month . ' ' . $date->format('Y') : $date->format('F Y');
                InfosMonthYearPublication::firstOrCreate(['date_name' => $dateName]);

                // ---- Création de la publication par catégorie ----
                if ($categories->isEmpty()) {
                    $publication = Publication::create([
                        'title' => $value['title']['rendered'],
                        'title_truncate' => Str::words(strip_tags($value['title']['rendered']), 10, ' ...'),
                        'slug' => Str::slug(strip_tags($value['title']['rendered'])),
                        'date_name' => $dateName,
                        'deja_citer' => 0,
                        'image_cover_url' => $imageCoverUrl,
                        'content' => $value['content']['rendered'],
                        'truncate_content' => Str::words(strip_tags($value['excerpt']['rendered']), 20, ' ...'),
                        'truncate_content_max' => $value['excerpt']['rendered'],
                        'status' => $value['status'] === 'publish' ? 1 : 0,
                        'comment_status' => $value['comment_status'] === 'open' ? 1 : 0,
                        'views_count' => rand(351, 2564),
                        'likes_count' => rand(123, 554),
                        'shares_count' => 0,
                        'comment_count' => 0,
                        'date_publish' => $value['date'],
                        'author_id' => $author->id,
                        'author_slug' => $author->slug,
                        'author_name' => $author->authorName,
                        'type_publication_id' => $typePublication->id,
                        'type_publication_name' => $typePublication->name,
                        'type_publication_slug' => $typePublication->slug,
                        'category_id' => null,
                        'category_name' => null,
                        'category_slug' => null,
                        'user_id' => 1,
                        'source' => 'Togoactualité',
                        'wp_article_id' => $value['id'],
                    ]);

                    // Attacher tags
                    foreach ($tags as $tag) {
                        PublicationTag::firstOrCreate(
                            ['publication_id' => $publication->id, 'tag_id' => $tag->id],
                            ['date_publish' => $value['date']]
                        );
                    }

                    // Attacher fichier
                    if ($fileId) {
                        PublicationFile::firstOrCreate(
                            ['publication_id' => $publication->id, 'file_id' => $fileId],
                            ['date_publish' => $value['date']]
                        );
                    }

                    $createdCount++;
                } else {
                    foreach ($categories as $index => $category) {
                        $deja_citer = $index === 0 ? 0 : 1;

                        $publication = Publication::create([
                            'title' => $value['title']['rendered'],
                            'title_truncate' => Str::words(strip_tags($value['title']['rendered']), 10, ' ...'),
                            'slug' => Str::slug(strip_tags($value['title']['rendered'])) . ($index > 0 ? "-{$category->slug}" : ""),
                            'date_name' => $dateName,
                            'deja_citer' => $deja_citer,
                            'image_cover_url' => $imageCoverUrl,
                            'content' => $value['content']['rendered'],
                            'truncate_content' => Str::words(strip_tags($value['excerpt']['rendered']), 20, ' ...'),
                            'truncate_content_max' => $value['excerpt']['rendered'],
                            'status' => $value['status'] === 'publish' ? 1 : 0,
                            'comment_status' => $value['comment_status'] === 'open' ? 1 : 0,
                            'views_count' => rand(351, 2564),
                            'likes_count' => rand(123, 554),
                            'shares_count' => 0,
                            'comment_count' => 0,
                            'date_publish' => $value['date'],
                            'author_id' => $author->id,
                            'author_slug' => $author->slug,
                            'author_name' => $author->authorName,
                            'type_publication_id' => $typePublication->id,
                            'type_publication_name' => $typePublication->name,
                            'type_publication_slug' => $typePublication->slug,
                            'category_id' => $category->id,
                            'category_name' => $category->name,
                            'category_slug' => $category->slug,
                            'user_id' => 1,
                            'source' => 'Togoactualité',
                            'wp_article_id' => $value['id'],
                        ]);

                        foreach ($tags as $tag) {
                            PublicationTag::firstOrCreate(
                                ['publication_id' => $publication->id, 'tag_id' => $tag->id],
                                ['date_publish' => $value['date']]
                            );
                        }

                        if ($fileId) {
                            PublicationFile::firstOrCreate(
                                ['publication_id' => $publication->id, 'file_id' => $fileId],
                                ['date_publish' => $value['date']]
                            );
                        }

                        $createdCount++;
                    }
                }
            }

            $this->command->info("Page $i de publications traitée : $createdCount publications créées.");
        }

        $this->command->info('Import des publications terminé avec succès !');
    }
}
