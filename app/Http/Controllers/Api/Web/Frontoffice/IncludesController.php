<?php
namespace App\Http\Controllers\Api\Web\Frontoffice;
use App\Http\Controllers\Api\BaseController; 
use App\Models\Category; 
use App\Models\NewsLetter;
use App\Models\Tag;
use App\Models\Publication;  
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; 

// Définition du contrôleur IncludesController
class IncludesController extends BaseController
{
    /**
     * Fonction utilitaire pour remplacer des chaînes de caractères
     *
     * @param mixed $search   Valeur(s) à rechercher
     * @param mixed $replace  Valeur(s) de remplacement
     * @param mixed $subject  Chaîne cible
     * @return string
     */
    public function str_replace_all($search, $replace, $subject)
    {
        // Utilisation de la fonction native PHP str_replace
        return str_replace($search, $replace, $subject);
    }

    /**
     * Récupère les publications de economie
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function economieRequestData()
    {
        $economieData = Cache::remember(
            'economie_publications_header',
            now()->addMinutes(10),
            function () {
                return Publication::where([
                    ["status", 1],
                    ["type_publication_id", 1],
                    ["category_id", 12]
                ])
                ->latest('date_publish')
                ->take(4)
                ->get([
                    "id",
                    "content",
                    "truncate_content",
                    "title",
                    "slug",
                    "date_publish",
                    "author_name",
                    "author_slug",
                    "image_cover_url"
                ]);
            }
        );

        if ($economieData->isEmpty()) {
            return $this->sendError('Aucune publication de economie trouvée', [], 404);
        }

        return $this->sendResponse($economieData, 'Publications de economie récupérées');
    }

    /**
     * Récupère les publications internationales
     */
    public function internationalRequestData()
    {
        // Récupération des publications correspondant aux catégories définies
        $internationalData = Cache::remember(
            'international_publications_header', // Clé de cache unique
            now()->addMinutes(10), // Cache pendant 10 minutes
            function () {
                return Publication::where([
                    ["status", 1],
                    ["type_publication_id", 1],
                    ["category_id", 20]
                ])
                ->latest('date_publish')
                ->take(4)
                ->get([
                    "id",
                    "content",
                    "truncate_content",
                    "title",
                    "slug",
                    "date_publish",
                    "author_name",
                    "author_slug",
                    "image_cover_url"
                ]);
            }
        );

        if ($internationalData->isEmpty()) {
            return $this->sendError('Aucune publication de international trouvée', [], 404);
        }

        return $this->sendResponse($internationalData, 'Publications de international récupérées');
    }

    /**
     * Récupère les publications du Politique
     */
   public function politiqueRequestData()
    {
        // Récupération des publications correspondant aux catégories définies
        $politiqueData = Publication::where([
            ["status", 1],
            ["type_publication_id", 1],
            ["category_id", 27]
        ])
            ->latest('date_publish')
            ->take(4)
            ->get([
                "id",
                "content",
                "truncate_content",
                "title",
                "slug",
                "date_publish",
                "author_name",
                "author_slug",
                "image_cover_url"
            ]);

        // Vérification si aucune donnée n'est trouvée
        if ($politiqueData->isEmpty()) {
            return $this->sendError(
                'Aucune publication sur politique n\'est publiée.',
                [],
                404
            );
        }

        // Retour de la réponse avec succès
        return $this->sendResponse(
            $politiqueData,
            'Publications de politique récupérées'
        );
    }

   /**
     * Récupère les publications de diaspora
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function diasporaRequestData()
    {
        // Récupération des publications correspondant aux catégories définies
        $diasporaData = Cache::remember(
            'diaspora_publications_header', // Clé de cache unique
            now()->addMinutes(10), // Cache pendant 10 minutes
            function () {
                return Publication::where([
                    ["status", 1],
                    ["type_publication_id", 1],
                    ["category_id", 10]
                ])
                ->latest('date_publish')
                ->take(4)
                ->get([
                    "id",
                    "content",
                    "truncate_content",
                    "title",
                    "slug",
                    "date_publish",
                    "author_name",
                    "author_slug",
                    "image_cover_url"
                ]);
            }
        );

        // Vérification si aucune donnée n'est trouvée
        if ($diasporaData->isEmpty()) {
            return $this->sendError(
                'Aucune publication sur diaspora n\'est publiée.',
                [],
                404
            );
        }

        // Retour de la réponse avec succès
        return $this->sendResponse(
            $diasporaData,
            'Publications de diaspora récupérées'
        );
    }

     /**
     * Récupère les publications de societe
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function societeRequestData()
    {
        // Récupération des publications correspondant aux catégories définies
        $societeData = Cache::remember(
            'societe_publications_header',
            now()->addMinutes(10),
            function () {
                return Publication::where([
                    ["status", 1],
                    ["type_publication_id", 1],
                    ["category_id", 30]
                ])
                ->latest('date_publish')
                ->take(4)
                ->get([
                    "id",
                    "content",
                    "truncate_content",
                    "title",
                    "slug",
                    "date_publish",
                    "author_name",
                    "author_slug",
                    "image_cover_url"
                ]);
            }
        );
        // Vérification si aucune donnée n'est trouvée
        if ($societeData->isEmpty()) {
            return $this->sendError(
                'Aucune publication sur societe n\'est publiée.',
                [],
                404
            );
        }

        // Retour de la réponse avec succès
        return $this->sendResponse(
            $societeData,
            'Publications de societe récupérées'
        );
    }

    /**
     * Récupère les publications de la rubrique
     */
    public function rubriquesRequestData()
    {
       // Récupération des publications correspondant aux catégories définies
        $rubriquesData = Cache::remember(
            'rubriques_publications_header', // Clé de cache unique
            now()->addMinutes(10), // Cache pendant 10 minutes
            function () {
                return Publication::where([
                    ["status", 1],
                    ["type_publication_id", 1],
                    ["category_id", 28]
                ])
                ->latest('date_publish')
                ->take(4)
                ->get([
                    "id",
                    "content",
                    "truncate_content",
                    "title",
                    "slug",
                    "date_publish",
                    "author_name",
                    "author_slug",
                    "image_cover_url"
                ]);
            }
        );

        if ($rubriquesData->isEmpty()) {
            return $this->sendError('Aucune publication de rubriques trouvée', [], 404);
        }

        return $this->sendResponse($rubriquesData, 'Publications de rubriques récupérées');
    }

     
    /**
     * Enregistrement d'un email pour la newsletter
     */
    public function newsletterStoreRequest(Request $request)
    {
        // Validation des données envoyées
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255', 'unique:news_letters,email'],
        ], [
            'email.required' => 'Votre email est obligatoire.',
            'email.email' => 'Veuillez entrer un email valide.',
            'email.unique' => 'Cet email est déjà enregistré.',    
        ]);

        // Si validation échoue
        if ($validator->fails()) {
            return $this->sendError(
                'Erreur de validation',
                $validator->errors(),
                422
            );
        }

         // 🛡️ HONEYPOT (champ caché pour bots)
        if (!empty($request->input('website'))) {
            return $this->sendError('Bot détecté', [], 403);
        }

        // 🛡️ ANTI BOT - User Agent
        $userAgent = $request->header('User-Agent');
        if (preg_match('/bot|crawl|spider|curl|wget|python/i', $userAgent)) {
            return $this->sendError('Bot détecté', [], 403);
        }

        // 🛡️ ANTI BOT - temps humain
        $formTime = (int) $request->input('form_time');
        if ((time() - $formTime) < 1) {
            return $this->sendError('Action trop rapide détectée.', [], 422);
        }

        // Création de l'entrée newsletter
        $newsletter = NewsLetter::create([
            'email' => $request->email,
            'slug' => Str::slug($request->email),
        ]);

        // Vérifie si la création a échoué
        if (!$newsletter) {
            return $this->sendError(
                'Impossible d\'enregistrer cet email.',
                [],
                500
            );
        }

        // Retour succès
        return $this->sendResponse(
            $newsletter,
            'Email enregistré avec succès.'
        );
    }
    /**
    * Récupère les tags populaires
    */
    
    public function tagsRequestData()
    {
        $tags = Cache::remember(
            'popular_tags_footer',
            now()->addMinutes(10),
            function () {
                return Tag::orderByDesc('count_publications')
                    ->take(10)
                    ->get();
            }
        );

        if ($tags->isEmpty()) {
            return $this->sendError(
                'Aucun mot clé disponible.',
                [],
                404
            );
        }

        return $this->sendResponse(
            $tags,
            'Liste des mots clés populaires'
        );
    }

    /**
     * Récupère les catégories populaires
     */
    public function categoryRequestData()
    {
        $categories = Cache::remember(
            'popular_categories_footer',
            now()->addMinutes(10),
            function () {
                return Category::orderByDesc('count_publications')
                    ->take(8)
                    ->get();
            }
        );

        if ($categories->isEmpty()) {
            return $this->sendError(
                'Aucune catégorie disponible.',
                [],
                404
            );
        }

        return $this->sendResponse(
            $categories,
            'Liste des catégories populaires'
        );
    }

        
    /**
     * Récupère les publications populaires récentes
     */
    public function publicationsRequestData()
    {
        $publications = Cache::remember(
            'popular_publications_footer',
            now()->addMinutes(10),
            function () {
                return Publication::where([
                    ['status', 1],
                    ['type_publication_id', 1],
                ])
                ->whereDate('date_publish', '>', '2025-12-31')
                ->orderByDesc('views_count')
                ->take(2)
                ->get([
                    "id",
                    "content",
                    "truncate_content",
                    "title_truncate",
                    "title",
                    "slug",
                    "date_publish",
                    "author_name",
                    "author_slug",
                    "category_name",
                    "category_slug",
                    "image_cover_url"
                ]);
            }
        );

        if ($publications->isEmpty()) {
            return $this->sendError(
                'Aucune publication populaire trouvée.',
                [],
                404
            );
        }

        return $this->sendResponse(
            $publications,
            'Liste des publications populaires'
        );
    }
}