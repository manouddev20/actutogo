<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Category;
use App\Models\MediaFile;
use App\Models\Publication;
use App\Models\Tag;
use App\Models\TypeMediaFile;
use App\Models\TypePublication;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PublicationTableSeeder extends Seeder
{
    private string $apiUrl = 'https://togoactualite.com/wp-json/wp/v2/posts';

    public function run(): void
    {
        $this->command->info('Début du transfert des publications WordPress...');

        $user = User::whereHas('role', function ($query) {
            $query->where('slug', 'administrateur');
        })->first();

        if (!$user) {
            throw new RuntimeException('Aucun utilisateur administrateur trouvé.');
        }

        $typePublication = TypePublication::where('slug', 'article')->first();

        if (!$typePublication) {
            throw new RuntimeException('Le type de publication "article" est introuvable.');
        }

        $response = Http::acceptJson()
            ->timeout(120)
            ->retry(3, 1000)
            ->get($this->apiUrl, [
                'per_page' => 100,
                'page' => 1,
                '_embed' => true,
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Impossible de récupérer les publications WordPress. HTTP ' . $response->status()
            );
        }

        $total = (int) $response->header('X-WP-Total');
        $totalPages = (int) $response->header('X-WP-TotalPages');

        $this->command->info('Total des publications WordPress : ' . $total);
        $this->command->info('Nombre total de pages : ' . $totalPages);
        $this->command->info('Début du transfert avec 100 publications par page.');

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $authorsCreated = 0;
        $mediaCreated = 0;
        $categoriesLinked = 0;
        $tagsLinked = 0;
        $mediaLinked = 0;
        $processed = 0;

        for ($page = 1; $page <= $totalPages; $page++) {
            if ($page === 1) {
                $posts = $response->json();
            } else {
                $pageResponse = Http::acceptJson()
                    ->timeout(120)
                    ->retry(3, 1000)
                    ->get($this->apiUrl, [
                        'per_page' => 100,
                        'page' => $page,
                        '_embed' => true,
                    ]);

                if ($pageResponse->failed()) {
                    $this->command->error(
                        'Erreur lors de la récupération de la page ' .
                        $page .
                        ' : HTTP ' .
                        $pageResponse->status()
                    );

                    continue;
                }

                $posts = $pageResponse->json();
            }

            $batchCount = count($posts);

            $this->command->info(
                'Page ' .
                $page .
                '/' .
                $totalPages .
                ' - ' .
                $batchCount .
                ' publications à traiter.'
            );

            foreach ($posts as $post) {
                try {
                    $result = $this->importPublication(
                        $post,
                        $user,
                        $typePublication,
                        $authorsCreated,
                        $mediaCreated
                    );

                    if ($result['status'] === 'created') {
                        $created++;
                    } elseif ($result['status'] === 'updated') {
                        $updated++;
                    } else {
                        $skipped++;
                    }

                    $categoriesLinked += $result['categories'];
                    $tagsLinked += $result['tags'];
                    $mediaLinked += $result['media'];

                    $processed++;

                    $this->command->info(
                        'Publication ' .
                        $processed .
                        '/' .
                        $total .
                        ' transférée : ' .
                        Str::limit($result['title'], 80)
                    );
                } catch (\Throwable $e) {
                    $processed++;

                    $this->command->error(
                        'Publication ' .
                        $processed .
                        '/' .
                        $total .
                        ' ignorée : ' .
                        $e->getMessage()
                    );
                }
            }

            $this->command->info(
                'Progression : ' .
                $processed .
                '/' .
                $total .
                ' publications traitées.'
            );

            $this->command->info(
                'Page ' .
                $page .
                '/' .
                $totalPages .
                ' terminée.'
            );
        }

        $this->command->newLine();

        $this->command->info('Transfert des publications terminé.');
        $this->command->info('Publications créées : ' . $created);
        $this->command->info('Publications mises à jour : ' . $updated);
        $this->command->info('Publications ignorées : ' . $skipped);
        $this->command->info('Auteurs créés automatiquement : ' . $authorsCreated);
        $this->command->info('Fichiers médias créés automatiquement : ' . $mediaCreated);
        $this->command->info('Relations catégories : ' . $categoriesLinked);
        $this->command->info('Relations tags : ' . $tagsLinked);
        $this->command->info('Relations médias : ' . $mediaLinked);
        $this->command->info('Total traité : ' . $processed . '/' . $total);
    }

    private function importPublication(
        array $post,
        User $user,
        TypePublication $typePublication,
        int &$authorsCreated,
        int &$mediaCreated
    ): array {
        $wpPublicationId = isset($post['id']) ? (int) $post['id'] : null;

        if (!$wpPublicationId) {
            throw new RuntimeException('Identifiant WordPress de publication introuvable.');
        }

        $author = $this->resolveAuthor(
            $post,
            $user,
            $authorsCreated
        );

        if (!$author) {
            throw new RuntimeException(
                'Auteur introuvable pour la publication WordPress #' . $wpPublicationId
            );
        }

        $coverMediaFile = $this->resolveFeaturedMedia(
            $post,
            $user,
            $mediaCreated
        );

        $content = $post['content']['rendered'] ?? '';
        $title = trim(strip_tags($post['title']['rendered'] ?? ''));

        if ($title === '') {
            throw new RuntimeException(
                'Titre vide pour la publication WordPress #' . $wpPublicationId
            );
        }

        $summary = $post['excerpt']['rendered'] ?? null;
        $excerpt = $this->createShortExcerpt($content);

        $contentMediaFiles = $this->resolveContentMedia(
            $content,
            $user,
            $mediaCreated
        );

        $categories = $this->resolveCategories($post);
        $tags = $this->resolveTags($post);

        $publishedAt = null;

        if (!empty($post['date'])) {
            try {
                $publishedAt = date('Y-m-d H:i:s', strtotime($post['date']));
            } catch (\Throwable $e) {
                $publishedAt = null;
            }
        }

        $existingPublication = Publication::withTrashed()
            ->where('wp_publication_id', $wpPublicationId)
            ->first();

        $isNew = !$existingPublication;

        if ($existingPublication) {
            if ($existingPublication->trashed()) {
                $existingPublication->restore();
            }

            $viewsCount = (int) $existingPublication->views_count;
            $likesCount = (int) $existingPublication->likes_count;
            $commentsCount = (int) $existingPublication->comments_count;
            $sharesCount = (int) $existingPublication->shares_count;
        } else {
            $viewsCount = random_int(500, 1800);
            $likesCount = random_int(1, $viewsCount - 1);
            $commentsCount = random_int(1, $viewsCount - 1);
            $sharesCount = random_int(1, $viewsCount - 1);
        }

        $publicationData = [
            'user_id' => $user->id,
            'type_publication_id' => $typePublication->id,
            'author_id' => $author->id,
            'cover_media_file_id' => $coverMediaFile?->id,
            'wp_publication_id' => $wpPublicationId,
            'title' => $title,
            'slug' => Str::slug($title),
            'title_truncated' => $this->truncateByWords($title, 7),
            'content' => $content,
            'summary' => $summary,
            'excerpt' => $excerpt,
            'views_count' => $viewsCount,
            'likes_count' => $likesCount,
            'shares_count' => $sharesCount,
            'comments_count' => $commentsCount,
            'source' => $post['link'] ?? null,
            'published_at' => $publishedAt,
            'status' => true,
        ];

        if ($existingPublication) {
            $existingPublication->update($publicationData);
            $publication = $existingPublication;
        } else {
            $publication = Publication::create($publicationData);
        }

        $publication->categories()->sync($categories);
        $publication->tags()->sync($tags);

        $mediaIds = [];

        if ($coverMediaFile) {
            $mediaIds[$coverMediaFile->id] = [
                'role' => 'cover',
                'position' => 0,
            ];
        }

        $position = 1;

        foreach ($contentMediaFiles as $mediaFile) {
            if (!$mediaFile) {
                continue;
            }

            if (isset($mediaIds[$mediaFile->id])) {
                continue;
            }

            $mediaIds[$mediaFile->id] = [
                'role' => 'content',
                'position' => $position,
            ];

            $position++;
        }

        $publication->mediaFiles()->sync($mediaIds);

        return [
            'status' => $isNew ? 'created' : 'updated',
            'title' => $title,
            'categories' => count($categories),
            'tags' => count($tags),
            'media' => count($mediaIds),
        ];
    }

    private function resolveAuthor(
        array $post,
        User $user,
        int &$authorsCreated
    ): ?Author {
        $wpAuthorId = isset($post['author'])
            ? (int) $post['author']
            : null;

        $embeddedAuthors = $post['_embedded']['author'] ?? [];

        if (!$wpAuthorId && !empty($embeddedAuthors)) {
            $wpAuthorId = isset($embeddedAuthors[0]['id'])
                ? (int) $embeddedAuthors[0]['id']
                : null;
        }

        if (!$wpAuthorId) {
            return null;
        }

        $author = Author::withTrashed()
            ->where('wp_author_id', $wpAuthorId)
            ->first();

        if ($author) {
            if ($author->trashed()) {
                $author->restore();
            }

            return $author;
        }

        $wpAuthor = null;

        if (!empty($embeddedAuthors)) {
            $wpAuthor = $embeddedAuthors[0];
        }

        if (!$wpAuthor) {
            $response = Http::acceptJson()
                ->timeout(120)
                ->retry(3, 1000)
                ->get(
                    'https://togoactualite.com/wp-json/wp/v2/users/' .
                    $wpAuthorId
                );

            if ($response->successful()) {
                $wpAuthor = $response->json();
            }
        }

        $authorName = null;

        if ($wpAuthor) {
            $authorName = trim(
                strip_tags(
                    $wpAuthor['name'] ??
                    $wpAuthor['slug'] ??
                    ''
                )
            );
        }

        if (!$authorName) {
            $authorName = trim(
                $post['yoast_head_json']['author'] ?? ''
            );
        }

        if (!$authorName) {
            $authorName = 'Auteur WordPress ' . $wpAuthorId;
        }

        $slug = Str::slug($authorName);

        if ($slug === '') {
            $slug = 'auteur-wordpress-' . $wpAuthorId;
        }

        $existingSlug = Author::withTrashed()
            ->where('slug', $slug)
            ->where(function ($query) use ($wpAuthorId) {
                $query->whereNull('wp_author_id')
                    ->orWhere('wp_author_id', '!=', $wpAuthorId);
            })
            ->exists();

        if ($existingSlug) {
            $slug .= '-' . $wpAuthorId;
        }

        $description = $wpAuthor['description'] ?? null;

        $author = Author::create([
            'created_by' => $user->id,
            'user_id' => null,
            'nom' => null,
            'prenom' => null,
            'nom_complet' => $authorName,
            'nom_auteur' => $authorName,
            'slug' => $slug,
            'telephone' => null,
            'adresse' => null,
            'description' => $description,
            'status' => true,
            'wp_author_id' => $wpAuthorId,
        ]);

        $authorsCreated++;

        return $author;
    }

    private function resolveFeaturedMedia(
        array $post,
        User $user,
        int &$mediaCreated
    ): ?MediaFile {
        $embeddedMedia = $post['_embedded']['wp:featuredmedia'] ?? [];

        if (empty($embeddedMedia)) {
            return null;
        }

        $media = $embeddedMedia[0] ?? null;

        if (!$media) {
            return null;
        }

        return $this->resolveMediaFile(
            $media,
            $user,
            $mediaCreated
        );
    }

    private function resolveContentMedia(
        string $content,
        User $user,
        int &$mediaCreated
    ): array {
        if (trim($content) === '') {
            return [];
        }

        libxml_use_internal_errors(true);

        $dom = new DOMDocument();

        $loaded = $dom->loadHTML(
            '<?xml encoding="utf-8" ?>' . $content,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        if (!$loaded) {
            libxml_clear_errors();
            return [];
        }

        $xpath = new DOMXPath($dom);
        $images = $xpath->query('//img');

        $mediaFiles = [];

        if ($images) {
            foreach ($images as $image) {
                $sourceUrl = trim($image->getAttribute('src'));

                if ($sourceUrl === '') {
                    continue;
                }

                $mediaFile = $this->findMediaByUrl($sourceUrl);

                if (!$mediaFile) {
                    $mediaFile = $this->createMediaFromUrl(
                        $sourceUrl,
                        $user,
                        $mediaCreated
                    );
                }

                if ($mediaFile) {
                    $mediaFiles[$mediaFile->id] = $mediaFile;
                }
            }
        }

        libxml_clear_errors();

        return array_values($mediaFiles);
    }

    private function resolveMediaFile(
        array $media,
        User $user,
        int &$mediaCreated
    ): ?MediaFile {
        $wpFileId = isset($media['id'])
            ? (int) $media['id']
            : null;

        if ($wpFileId) {
            $existing = MediaFile::withTrashed()
                ->where('wp_file_id', $wpFileId)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }

                return $existing;
            }
        }

        $sourceUrl = $media['source_url'] ?? null;

        if (!$sourceUrl) {
            return null;
        }

        $existing = $this->findMediaByUrl($sourceUrl);

        if ($existing) {
            if ($wpFileId && !$existing->wp_file_id) {
                $existing->update([
                    'wp_file_id' => $wpFileId,
                ]);
            }

            return $existing;
        }

        return $this->createMediaFromWordPress(
            $media,
            $user,
            $mediaCreated
        );
    }

    private function createMediaFromWordPress(
        array $media,
        User $user,
        int &$mediaCreated
    ): ?MediaFile {
        $sourceUrl = $media['source_url'] ?? null;

        if (!$sourceUrl) {
            return null;
        }

        $wpFileId = isset($media['id'])
            ? (int) $media['id']
            : null;

        $mimeType = $media['mime_type'] ?? null;
        $extension = $this->detectExtension(
            $mimeType,
            $sourceUrl
        );

        $typeSlug = $this->detectMediaType($mimeType);

        $mediaType = TypeMediaFile::where('slug', $typeSlug)->first();

        if (!$mediaType) {
            $mediaType = TypeMediaFile::where('slug', 'application')->first();
        }

        if (!$mediaType) {
            throw new RuntimeException(
                'Aucun type de média trouvé pour : ' . $typeSlug
            );
        }

        $title = trim(
            strip_tags(
                $media['title']['rendered'] ??
                basename(parse_url($sourceUrl, PHP_URL_PATH) ?? 'media')
            )
        );

        $slug = Str::slug($title);

        if ($slug === '') {
            $slug = 'media';
        }

        if ($wpFileId) {
            $slug .= '-' . $wpFileId;
        } else {
            $slug .= '-' . Str::random(8);
        }

        $details = $media['media_details'] ?? [];

        $width = isset($details['width'])
            ? (int) $details['width']
            : null;

        $height = isset($details['height'])
            ? (int) $details['height']
            : null;

        $size = isset($details['filesize'])
            ? (int) $details['filesize']
            : null;

        $fileName = basename(
            parse_url($sourceUrl, PHP_URL_PATH) ?? $title
        );

        $mediaFile = MediaFile::create([
            'type_media_file_id' => $mediaType->id,
            'user_id' => $user->id,
            'wp_file_id' => $wpFileId,
            'name' => $title !== '' ? $title : $fileName,
            'original_name' => $fileName,
            'slug' => $slug,
            'file_url' => $sourceUrl,
            'path' => null,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $size,
            'width' => $width,
            'height' => $height,
            'duration' => null,
            'title' => $title,
            'caption' => $media['caption']['rendered'] ?? null,
            'alt_text' => $media['alt_text'] ?? null,
            'description' => $media['description']['rendered'] ?? null,
            'status' => true,
        ]);

        $mediaCreated++;

        return $mediaFile;
    }

    private function createMediaFromUrl(
        string $sourceUrl,
        User $user,
        int &$mediaCreated
    ): ?MediaFile {
        $existing = $this->findMediaByUrl($sourceUrl);

        if ($existing) {
            return $existing;
        }

        $extension = $this->detectExtension(
            null,
            $sourceUrl
        );

        $mimeType = $this->detectMimeType($extension);

        $typeSlug = $this->detectMediaType($mimeType);

        $mediaType = TypeMediaFile::where('slug', $typeSlug)->first();

        if (!$mediaType) {
            $mediaType = TypeMediaFile::where('slug', 'application')->first();
        }

        if (!$mediaType) {
            return null;
        }

        $fileName = basename(
            parse_url($sourceUrl, PHP_URL_PATH) ?? 'media'
        );

        $name = pathinfo($fileName, PATHINFO_FILENAME);

        if ($name === '') {
            $name = 'media';
        }

        $slug = Str::slug($name) . '-' . Str::random(8);

        return MediaFile::create([
            'type_media_file_id' => $mediaType->id,
            'user_id' => $user->id,
            'wp_file_id' => null,
            'name' => $name,
            'original_name' => $fileName,
            'slug' => $slug,
            'file_url' => $sourceUrl,
            'path' => null,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => null,
            'width' => null,
            'height' => null,
            'duration' => null,
            'title' => $name,
            'caption' => null,
            'alt_text' => null,
            'description' => null,
            'status' => true,
        ]);
    }

    private function findMediaByUrl(string $sourceUrl): ?MediaFile
    {
        return MediaFile::withTrashed()
            ->where('file_url', $sourceUrl)
            ->first();
    }

    private function resolveCategories(array $post): array
    {
        $categories = [];

        $terms = $post['_embedded']['wp:term'] ?? [];

        foreach ($terms as $taxonomyTerms) {
            foreach ($taxonomyTerms as $term) {
                if (($term['taxonomy'] ?? null) !== 'category') {
                    continue;
                }

                $wpCategoryId = isset($term['id'])
                    ? (int) $term['id']
                    : null;

                if (!$wpCategoryId) {
                    continue;
                }

                $category = Category::where(
                    'wp_category_id',
                    $wpCategoryId
                )->first();

                if ($category) {
                    $categories[] = $category->id;
                }
            }
        }

        return array_values(array_unique($categories));
    }

    private function resolveTags(array $post): array
    {
        $tags = [];

        $terms = $post['_embedded']['wp:term'] ?? [];

        foreach ($terms as $taxonomyTerms) {
            foreach ($taxonomyTerms as $term) {
                if (($term['taxonomy'] ?? null) !== 'post_tag') {
                    continue;
                }

                $wpTagId = isset($term['id'])
                    ? (int) $term['id']
                    : null;

                if (!$wpTagId) {
                    continue;
                }

                $tag = Tag::where(
                    'wp_tag_id',
                    $wpTagId
                )->first();

                if ($tag) {
                    $tags[] = $tag->id;
                }
            }
        }

        return array_values(array_unique($tags));
    }

    private function createShortExcerpt(string $content): ?string
    {
        $text = html_entity_decode(
            strip_tags($content),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text);

        if ($text === '') {
            return null;
        }

        return Str::limit($text, 250);
    }

    private function detectMediaType(?string $mimeType): string
    {
        if (!$mimeType) {
            return 'application';
        }

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        if (str_starts_with($mimeType, 'text/')) {
            return 'text';
        }

        return 'application';
    }

    private function detectExtension(
        ?string $mimeType,
        ?string $sourceUrl
    ): ?string {
        if ($sourceUrl) {
            $path = parse_url($sourceUrl, PHP_URL_PATH);

            if ($path) {
                $extension = pathinfo($path, PATHINFO_EXTENSION);

                if ($extension !== '') {
                    return strtolower($extension);
                }
            }
        }

        if ($mimeType) {
            $map = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'image/svg+xml' => 'svg',
                'video/mp4' => 'mp4',
                'video/webm' => 'webm',
                'audio/mpeg' => 'mp3',
                'audio/mp3' => 'mp3',
                'application/pdf' => 'pdf',
                'text/plain' => 'txt',
            ];

            return $map[$mimeType] ?? null;
        }

        return null;
    }

    private function detectMimeType(?string $extension): string
    {
        if (!$extension) {
            return 'application/octet-stream';
        }

        $map = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
        ];

        return $map[strtolower($extension)]
            ?? 'application/octet-stream';
    }

    private function truncateByWords(string $text, int $words = 10): string
    {
        $text = trim(strip_tags($text));
        $parts = preg_split('/\s+/u', $text);

        if (count($parts) <= $words) {
            return $text;
        }

        return implode(' ', array_slice($parts, 0, $words)) . '...';
    }
}