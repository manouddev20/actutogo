<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CommentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $baseUrl = "http://www.togoactualite.com/wp-json/wp/v2/comments";
        $perPage = 100;

        // Première page pour récupérer le nombre total de pages
        $response = Http::get("$baseUrl?per_page=$perPage&page=1");

        if (!$response->successful()) {
            $this->command->error(
                '❌ Impossible de récupérer les commentaires depuis WordPress.'
            );

            return;
        }

        $totalPages = intval(
            $response->header('x-wp-totalpages', 1)
        );

        $this->command->info(
            "🔎 Total pages de commentaires à traiter : $totalPages"
        );

        $totalFetched  = 0;
        $totalImported = 0;

        for ($page = 1; $page <= $totalPages; $page++) {

            $response = Http::get(
                "$baseUrl?per_page=$perPage&page=$page"
            );

            if (!$response->successful()) {
                $this->command->warn(
                    "⚠️ Échec récupération page $page " .
                    "(status: {$response->status()})"
                );

                continue;
            }

            $comments = $response->json();

            $fetchedThisPage = count($comments);
            $insertedThisPage = 0;

            foreach ($comments as $value) {

                /*
                |--------------------------------------------------------------------------
                | Récupérer la publication correspondante
                |--------------------------------------------------------------------------
                */

                $article = Publication::where(
                    'wp_publication_id',
                    $value['post'] ?? null
                )->first();

                if (!$article) {
                    $this->command->warn(
                        "⚠️ Publication WP #{$value['post']} introuvable."
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Vérifier si le commentaire existe déjà
                |--------------------------------------------------------------------------
                |
                | Il faut avoir wp_comment_id dans la table comments.
                |
                */

                $comment = Comment::where(
                    'wp_comment_id',
                    $value['id']
                )->first();

                if ($comment) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Nettoyer le contenu
                |--------------------------------------------------------------------------
                */

                $content = $value['content']['rendered'] ?? '';

                /*
                | WordPress renvoie du HTML.
                |
                | Si tu veux conserver le HTML :
                | $content = $value['content']['rendered'] ?? '';
                |
                | Si tu veux uniquement du texte :
                */

                $content = trim($content);

                /*
                |--------------------------------------------------------------------------
                | Nom et email de l'auteur
                |--------------------------------------------------------------------------
                */

                $authorName = trim(
                    $value['author_name'] ?? 'Utilisateur'
                );

                $authorEmail = null;

                /*
                |--------------------------------------------------------------------------
                | Création du commentaire
                |--------------------------------------------------------------------------
                */

                $comment = Comment::create([
                    'publication_id' => $article->id,

                    // Import WordPress = visiteur/non-utilisateur
                    'user_id' => null,

                    // Commentaire parent
                    'parent_id' => null,

                    // Informations de l'auteur WordPress
                    'full_name' => $authorName,
                    'email' => $authorEmail,

                    'content' => $content,

                    // On considère les anciens commentaires
                    // WordPress comme approuvés
                    'status' => 1,

                    // Date originale WordPress
                    'created_at' => $value['date']
                        ?? now(),

                    'updated_at' => $value['modified']
                        ?? $value['date']
                        ?? now(),

                    // ID WordPress
                    'wp_comment_id' => $value['id'],
                ]);

                /*
                |--------------------------------------------------------------------------
                | Incrémenter le compteur de commentaires
                |--------------------------------------------------------------------------
                */

                $article->increment('comment_count');

                $insertedThisPage++;
            }

            $totalFetched += $fetchedThisPage;
            $totalImported += $insertedThisPage;

            $this->command->info(
                "📄 Page $page traitée : " .
                "$fetchedThisPage récupérés, " .
                "$insertedThisPage insérés " .
                "(Total : $totalImported)"
            );
        }

        $this->command->info(
            "✅ Import terminé : " .
            "$totalFetched récupérés, " .
            "$totalImported insérés."
        );
    }
}