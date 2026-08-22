<?php

namespace Database\Seeders;

use App\Models\FileType;
use App\Models\MediaFile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FileTableSeeder extends Seeder
{
    public function run(): void
    {
        $perPage = 100;

        /*
         * Utilisateur créateur des fichiers importés.
         */
        $user = User::query()->first();

        if (!$user) {
            $this->command->error(
                '❌ Aucun utilisateur trouvé. '
                    . 'Exécutez d\'abord UserTableSeeder.'
            );

            return;
        }

        /*
         * Récupération de tous les types de fichiers.
         */
        $typeFiles = FileType::query()->get();

        if ($typeFiles->isEmpty()) {
            $this->command->error(
                '❌ Aucun type de fichier trouvé. '
                    . 'Exécutez d\'abord FileTypeTableSeeder.'
            );

            return;
        }

        $grandTotalFetched = 0;
        $grandTotalInserted = 0;

        foreach ($typeFiles as $typeFile) {

            $this->command->info(
                "📁 Début de l'import : {$typeFile->name}"
            );

            /*
             * Première requête pour récupérer
             * les informations de pagination.
             */
            try {
                $response = Http::timeout(60)->get(
                    'http://www.togoactualite.com/wp-json/wp/v2/media',
                    [
                        'media_type' => $typeFile->slug_wp,
                        'per_page' => $perPage,
                        'page' => 1,
                    ]
                );
            } catch (\Throwable $exception) {
                $this->command->warn(
                    "⚠️ Erreur de connexion pour {$typeFile->name} : "
                        . $exception->getMessage()
                );

                continue;
            }

            if (!$response->successful()) {
                $this->command->warn(
                    "⚠️ Impossible de récupérer les médias "
                        . "pour le type {$typeFile->name}."
                );

                continue;
            }

            /*
             * Nombre total de pages et de fichiers.
             */
            $totalPages = (int) $response->header(
                'x-wp-totalpages',
                1
            );

            $totalFiles = (int) $response->header(
                'x-wp-total',
                0
            );

            $this->command->info(
                "📊 Type {$typeFile->name} : "
                    . "{$totalFiles} fichiers sur {$totalPages} page(s)."
            );

            $totalFetchedType = 0;
            $totalInsertedType = 0;

            /*
             * Traitement page par page.
             */
            for ($page = 1; $page <= $totalPages; $page++) {

                try {
                    $mediasResponse = Http::timeout(60)->get(
                        'http://www.togoactualite.com/wp-json/wp/v2/media',
                        [
                            'media_type' => $typeFile->slug_wp,
                            'per_page' => $perPage,
                            'page' => $page,
                        ]
                    );
                } catch (\Throwable $exception) {
                    $this->command->warn(
                        "⚠️ Erreur de connexion page {$page} "
                            . "pour {$typeFile->name} : "
                            . $exception->getMessage()
                    );

                    continue;
                }

                if (!$mediasResponse->successful()) {
                    $this->command->warn(
                        "⚠️ Échec de récupération de la page {$page} "
                            . "pour le type {$typeFile->name}."
                    );

                    continue;
                }

                $medias = $mediasResponse->json();

                $fetchedThisPage = count($medias);
                $insertedThisPage = 0;

                foreach ($medias as $media) {

                    /*
                     * Vérification de l'URL du fichier.
                     */
                    if (empty($media['source_url'])) {
                        continue;
                    }

                    /*
                     * Nom du fichier.
                     */
                    $fileName = $media['title']['rendered'] ?? null;

                    /*
                     * Nettoyage du nom.
                     */
                    if ($fileName) {
                        $fileName = strip_tags($fileName);
                    } else {
                        $fileName = basename(
                            parse_url(
                                $media['source_url'],
                                PHP_URL_PATH
                            )
                        );
                    }

                    /*
                     * Caption WordPress.
                     */
                    $caption = !empty($media['caption']['rendered'])
                        ? trim(
                            strip_tags(
                                $media['caption']['rendered']
                            )
                        )
                        : null;

                    /*
                     * Dates WordPress.
                     */
                    $createdAt = !empty($media['date_gmt'])
                        ? Carbon::parse($media['date_gmt'])
                        : now();

                    $updatedAt = !empty($media['modified_gmt'])
                        ? Carbon::parse($media['modified_gmt'])
                        : $createdAt;

                    /*
                     * Création ou mise à jour du fichier.
                     */
                    $file = MediaFile::updateOrCreate(
                        [
                            'wp_file_id' => $media['id'],
                        ],
                        [
                            'user_id' => $user->id,

                            'file_type_id' => $typeFile->id,

                            'wp_file_id' => $media['id'],

                            'file_name' => $fileName,

                            'file_slug' => Str::slug($fileName),

                            'file_url' => $media['source_url'],

                            'wp_file' => $media['source_url'],

                            'caption' => $caption,

                            'created_at' => $createdAt,

                            'updated_at' => $updatedAt,
                        ]
                    );

                    if ($file->wasRecentlyCreated) {
                        $insertedThisPage++;
                    }
                }

                $totalFetchedType += $fetchedThisPage;
                $totalInsertedType += $insertedThisPage;

                $this->command->info(
                    "📄 Page {$page}/{$totalPages} : "
                        . "{$fetchedThisPage} récupérés, "
                        . "{$insertedThisPage} créés."
                );
            }

            $this->command->info(
                "✅ Type {$typeFile->name} terminé : "
                    . "{$totalFetchedType} récupérés, "
                    . "{$totalInsertedType} créés."
            );

            $grandTotalFetched += $totalFetchedType;
            $grandTotalInserted += $totalInsertedType;
        }

        $this->command->info(
            "🎯 Import terminé : "
                . "{$grandTotalFetched} fichiers récupérés, "
                . "{$grandTotalInserted} nouveaux fichiers créés."
        );
    }
}
