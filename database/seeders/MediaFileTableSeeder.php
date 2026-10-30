<?php

namespace Database\Seeders;

use App\Models\MediaFile;
use App\Models\TypeMediaFile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MediaFileTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('==============================================');
        $this->command->info('DÉBUT DU TRANSFERT DES FICHIERS WORDPRESS');
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

        $baseUrl = 'https://togoactualite.com/wp-json/wp/v2/media';
        $perPage = 100;

        $this->command->info('Connexion à l’API WordPress...');
        $this->command->info('URL : ' . $baseUrl);
        $this->command->info('Nombre demandé par page : ' . $perPage);

        $response = Http::acceptJson()
            ->timeout(180)
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

        $totalFiles = (int) $response->header('X-WP-Total');
        $totalPages = (int) $response->header('X-WP-TotalPages');

        if ($totalFiles === 0) {
            $this->command->warn('Aucun fichier trouvé dans WordPress.');
            return;
        }

        if ($totalPages === 0) {
            $totalPages = (int) ceil($totalFiles / $perPage);
        }

        $this->command->info(
            'Nombre total de fichiers : ' . $totalFiles
        );

        $this->command->info(
            'Nombre total de pages : ' . $totalPages
        );

        $this->command->info(
            'Taille de chaque page : jusqu’à ' . $perPage . ' fichiers'
        );

        $transferred = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;

        for ($page = 1; $page <= $totalPages; $page++) {
            if ($page === 1) {
                $files = $response->json();
            } else {
                $pageResponse = Http::acceptJson()
                    ->timeout(180)
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

                $files = $pageResponse->json();
            }

            $pageTransferred = 0;
            $pageCreated = 0;
            $pageUpdated = 0;

            foreach ($files as $fileData) {
                if (
                    !isset($fileData['id']) ||
                    !isset($fileData['source_url']) ||
                    !isset($fileData['mime_type'])
                ) {
                    $skipped++;
                    continue;
                }

                $wpFileId = (int) $fileData['id'];
                $mimeType = strtolower(trim($fileData['mime_type']));

                $typeSlug = match (true) {
                    Str::startsWith($mimeType, 'image/') => 'image',
                    Str::startsWith($mimeType, 'video/') => 'video',
                    Str::startsWith($mimeType, 'audio/') => 'audio',
                    Str::startsWith($mimeType, 'text/') => 'text',
                    default => 'application',
                };

                $mediaFileType = TypeMediaFile::where(
                    'slug',
                    $typeSlug
                )->first();

                if (!$mediaFileType) {
                    $skipped++;
                    continue;
                }

                $title = isset($fileData['title']['rendered'])
                    ? trim(strip_tags($fileData['title']['rendered']))
                    : null;

                $caption = isset($fileData['caption']['rendered'])
                    ? trim(strip_tags($fileData['caption']['rendered']))
                    : null;

                $description = isset($fileData['description']['rendered'])
                    ? trim($fileData['description']['rendered'])
                    : null;

                $altText = isset($fileData['alt_text'])
                    ? trim($fileData['alt_text'])
                    : null;

                $slug = isset($fileData['slug'])
                    ? Str::slug($fileData['slug'])
                    : 'media-' . $wpFileId;

                $sourceUrl = $fileData['source_url'];

                $originalName = null;

                if (
                    isset($fileData['media_details']['file']) &&
                    $fileData['media_details']['file']
                ) {
                    $originalName = basename(
                        $fileData['media_details']['file']
                    );
                }

                if (!$originalName) {
                    $originalName = basename(
                        parse_url($sourceUrl, PHP_URL_PATH)
                    );
                }

                $extension = pathinfo(
                    $originalName,
                    PATHINFO_EXTENSION
                );

                $width = $fileData['media_details']['width'] ?? null;
                $height = $fileData['media_details']['height'] ?? null;
                $size = $fileData['media_details']['filesize'] ?? null;

                $duration = null;

                if (
                    isset($fileData['media_details']['length_formatted']) &&
                    isset($fileData['media_details']['length'])
                ) {
                    $duration = $fileData['media_details']['length'];
                }

                $data = [
                    'type_media_file_id' => $mediaFileType->id,
                    'user_id' => $user->id,
                    'wp_file_id' => $wpFileId,
                    'name' => $title ?: $originalName,
                    'original_name' => $originalName,
                    'slug' => $slug,
                    'file_url' => $sourceUrl,
                    'path' => null,
                    'mime_type' => $mimeType,
                    'extension' => $extension ?: null,
                    'size' => $size,
                    'width' => $width,
                    'height' => $height,
                    'duration' => $duration,
                    'title' => $title,
                    'caption' => $caption,
                    'alt_text' => $altText,
                    'description' => $description,
                    'status' => true,
                ];

                $mediaFile = MediaFile::withTrashed()
                    ->where('wp_file_id', $wpFileId)
                    ->first();

                if ($mediaFile) {
                    $mediaFile->update($data);

                    if ($mediaFile->trashed()) {
                        $mediaFile->restore();
                    }

                    $updated++;
                    $pageUpdated++;
                } else {
                    $mediaFile = MediaFile::create($data);

                    $created++;
                    $pageCreated++;
                }

                $transferred++;
                $pageTransferred++;
            }

            $this->command->info(
                'Page ' .
                $page .
                ' / ' .
                $totalPages .
                ' | Fichiers récupérés : ' .
                $pageTransferred .
                ' | Créés : ' .
                $pageCreated .
                ' | Mis à jour : ' .
                $pageUpdated .
                ' | Progression : ' .
                $transferred .
                ' / ' .
                $totalFiles
            );
        }

        $this->command->info('----------------------------------------------');
        $this->command->info(
            'Fichiers créés : ' . $created
        );
        $this->command->info(
            'Fichiers mis à jour : ' . $updated
        );
        $this->command->info(
            'Fichiers ignorés : ' . $skipped
        );
        $this->command->info(
            'Fichiers transférés : ' .
            $transferred .
            ' / ' .
            $totalFiles
        );
        $this->command->info(
            'Nombre de pages traitées : ' . $totalPages
        );
        $this->command->info('TRANSFERT DES FICHIERS TERMINÉ.');
        $this->command->info('==============================================');
    }
}