<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\Comment;
use App\Models\Commentator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CommentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $baseUrl = "http://www.togoactualite.com/wp-json/wp/v2/comments";
        $perPage = 100;

        // Première page pour déterminer le total
        $response = Http::get("$baseUrl?per_page=$perPage&page=1");

        if (!$response->successful()) {
            $this->command->error('❌ Impossible de récupérer les commentaires depuis WordPress.');
            return;
        }

        $totalPages = intval($response->header('x-wp-totalpages', 1));
        $this->command->info("🔎 Total pages de commentaires à traiter : $totalPages");

        $totalImported = 0;   // total insérés réellement en DB
        $totalFetched  = 0;   // total récupérés depuis l’API

        for ($page = 1; $page <= $totalPages; $page++) {
            $response = Http::get("$baseUrl?per_page=$perPage&page=$page");

            if (!$response->successful()) {
                $this->command->warn("⚠️ Échec récupération page $page (status: " . $response->status() . ")");
                continue;
            }

            $comments = $response->json();
            $fetchedThisPage = count($comments);
            $insertedThisPage = 0;

            foreach ($comments as $value) {
                // Vérifier si le commentateur existe déjà
                $commentator = Commentator::firstOrCreate(
                    ['wp_author_id' => $value['author']], // ID WP comme clé unique
                    [
                        'nom_complet' => $value['author_name'] ?? 'Inconnu',
                        'slug' => Str::slug($value['author_name'] ?? 'user') . '-' . $value['author'],
                        'date_publish' => date('Y-m-d H:i:s', strtotime($value['date'])),
                        'count_comments' => 0,
                    ]
                );

                // Récupérer l'article lié
                $article = Publication::where('wp_article_id', $value['post'])->first();

                if ($article) {
                    $comment = Comment::firstOrCreate(
                        ['wp_comment_id' => $value['id']], // clé unique WP
                        [
                            'publication_id' => $article->id,
                            'commentator_id' => $commentator->id,
                            'content' => $value['content']['rendered'] ?? '',
                            'date_publish' => date('Y-m-d H:i:s', strtotime($value['date']))
                        ]
                    );

                    if ($comment->wasRecentlyCreated) {
                        $commentator->increment('count_comments');
                        $article->increment('comment_count');
                        $insertedThisPage++;
                    }
                }
            }

            $totalFetched  += $fetchedThisPage;
            $totalImported += $insertedThisPage;

            $this->command->info("📄 Page $page traitée : $fetchedThisPage récupérés, $insertedThisPage insérés (Total insérés : $totalImported)");
        }

        $this->command->info("✅ Import terminé : $totalFetched récupérés au total, $totalImported insérés en base.");
    }
}
