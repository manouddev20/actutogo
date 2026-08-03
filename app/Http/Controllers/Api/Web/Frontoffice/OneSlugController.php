<?php

namespace App\Http\Controllers\Api\Web\Frontoffice;

use App\Http\Controllers\Api\BaseController;
use App\Models\Publication;
use App\Models\Author;
use App\Models\Category;
use App\Models\PublicationTag;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OneSlugController extends BaseController
{
    public function slug(Request $request, $slug)
    {

        if ($slug === 'forum') {

            return view('oneSlugPage.forum');

        } elseif ($slug === 'terms') {

            return view('oneSlugPage.terms');
        } elseif ($slug === 'privacy') {

            return view('oneSlugPage.privacy');
        } elseif ($slug === 'contact') {

            return view('oneSlugPage.contact');
        } elseif ($slug === 'about') {


            $users = User::where('status', 1)->paginate(10);

            return view('oneSlugPage.about', ['users' => $users]);
        } elseif ($slug === 'infos-pratiques') {

            return view('oneSlugPage.infos-pratiques');
        } elseif ($slug === 'ads.txt') {

            return view('adsense.ads');
        } elseif ($slug === 'all-category') {

            $categories = Category::query();

            // 🛡️ HONEYPOT (champ caché pour bots)
            if (!empty($request->input('website'))) {
                abort(403, 'Bot détecté');
            }

            // 🛡️ ANTI BOT - User Agent
            $userAgent = $request->header('User-Agent');
            if (preg_match('/bot|crawl|spider|curl|wget|python/i', $userAgent)) {
                abort(403, 'Bot détecté');
            }

            if($request->filled('query')){

                // 🛡️ ANTI BOT - temps humain
                $formTime = (int) $request->input('form_time');
                if ((time() - $formTime) < 1) {
                    return back()->withErrors([
                        'query' => 'Action trop rapide détectée.'
                    ])->withInput();
                }

                // 🛡️ VALIDATION + REGEX
                $validator = Validator::make($request->all(), [
                    'query' => [
                        'required',
                        'string',
                        'min:2',
                        'max:100',
                        'regex:/^[\pL\s0-9\-\']+$/u'
                    ]
                ], [
                    'query.regex' => 'Recherche invalide : caractères non autorisés.',
                    'query.min' => 'Votre recherche est trop courte.',
                    'query.max' => 'Votre recherche est trop longue.'
                ]);

                if ($validator->fails()) {
                    return back()
                        ->withErrors($validator)
                        ->withInput();
                }

                // 🧹 NETTOYAGE INPUT
                $search = trim(strip_tags($request->input('query')));
                $search = strtolower($search);

                 $queryBuilder = Category::where(function($q) use ($search){
                        $q->where('categories.name', 'like', "%{$search}%")
                        ->orWhere('categories.slug', 'like', "%{$search}%")
                        ->orWhere('categories.date_publish', 'like', "%{$search}%")
                        ->orWhere('categories.count_publications', 'like', "%{$search}%");
                    });

                $categories = $queryBuilder
                    ->orderBy('categories.count_publications', 'desc')
                    ->paginate(9);

                $categoryCount = $queryBuilder->count();

                 
            } else {

                $categories = DB::table("categories")
                    ->orderBy('categories.count_publications', 'desc')
                    ->paginate(9);

                $categoryCount = Category::count();
            }

            return view('oneSlugPage.all-category', ['categories' => $categories, 'categoryCount' => $categoryCount]);
        
        } elseif($slug === "search-posts"){

            // 🛡️ Vérifie s’il y a des publications
            $articles_count = Publication::where('status', 1)
                ->where('type_publication_id', 1)
                ->count();

            if($articles_count === 0){
                return view('errors.HomePageControlEmpty');
            }

            // 🛡️ HONEYPOT (champ caché pour bots)
            if (!empty($request->input('website'))) {
                abort(403, 'Bot détecté');
            }

            // 🛡️ ANTI BOT - User Agent
            $userAgent = $request->header('User-Agent');
            if (preg_match('/bot|crawl|spider|curl|wget|python/i', $userAgent)) {
                abort(403, 'Bot détecté');
            }

            if($request->filled('query')){

                // 🛡️ ANTI BOT - temps humain
                $formTime = (int) $request->input('form_time');
                if ((time() - $formTime) < 1) {
                    return back()->withErrors([
                        'query' => 'Action trop rapide détectée.'
                    ])->withInput();
                }

                // 🛡️ VALIDATION + REGEX
                $validator = Validator::make($request->all(), [
                    'query' => [
                        'required',
                        'string',
                        'min:2',
                        'max:100',
                        'regex:/^[\pL\s0-9\-\']+$/u'
                    ]
                ], [
                    'query.regex' => 'Recherche invalide : caractères non autorisés.',
                    'query.min' => 'Votre recherche est trop courte.',
                    'query.max' => 'Votre recherche est trop longue.'
                ]);

                if ($validator->fails()) {
                    return back()
                        ->withErrors($validator)
                        ->withInput();
                }

                // 🧹 NETTOYAGE INPUT
                $search = trim(strip_tags($request->input('query')));
                $search = strtolower($search);

                // 🔎 REQUÊTE SÉCURISÉE
                $queryBuilder = Publication::where('status', 1)
                    ->where('type_publication_id', 1)
                    ->where('deja_citer', 0)
                    ->where(function($q) use ($search){
                        $q->where('title', 'like', "%{$search}%")
                        ->orWhere('category_name', 'like', "%{$search}%")
                        ->orWhere('author_name', 'like', "%{$search}%");
                    });

                $articles = $queryBuilder
                    ->orderBy('date_publish', 'desc')
                    ->get();

                $count = $queryBuilder->count();

                return view('oneSlugPage.search-publications', [
                    'articles' => $articles,
                    'search' => $search,
                    'count' => $count
                ]);

            } else {

                $articles = Publication::where('status', 1)
                    ->where('type_publication_id', 1)
                    ->where('deja_citer', 0)
                    ->orderBy('date_publish', 'desc')
                    ->take(10)
                    ->get();

                return view('oneSlugPage.search-publications', [
                    'articles' => $articles,
                    'search' => false,
                    'count' => false
                ]);
            }
        } else {

            // 🧹 Sécurisation du slug
            $slug = trim(strip_tags($slug));

            // 📦 Recherche category + article en parallèle
            $category = Category::where('slug', $slug)->first();

            $article = Publication::where('slug', $slug)
                ->where('status', 1)
                ->where('type_publication_id', 1)
                ->first();

            // ❌ Aucun résultat
            if (!$category && !$article) {
                return view('errors.ErrorSlugPage');
            }

            /*
            |--------------------------------------------------------------------------
            | 📂 CAS 1 : CATEGORY PAGE
            |--------------------------------------------------------------------------
            */
            if ($category && !$article) {

                $articles_count = Publication::where('status', 1)
                    ->where('type_publication_id', 1)
                    ->count();

                if ($articles_count === 0) {
                    return view('errors.HomePageControlEmpty');
                }

                $otherCategory = Category::all();

                $articles = Publication::where('status', 1)
                    ->where('type_publication_id', 1)
                    ->where('category_id', $category->id)
                    ->orderBy('date_publish', 'desc')
                    ->paginate(6);

                $alireaussi = Publication::where('status', 1)
                    ->where('type_publication_id', 1)
                    ->where('deja_citer', 0)
                    ->orderBy('date_publish', 'desc')
                    ->take(5)
                    ->get();

                return view('oneSlugPage.category', [
                    'articles' => $articles,
                    'alireaussi' => $alireaussi,
                    'category' => $category,
                    'otherCategory' => $otherCategory
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 📰 CAS 2 : ARTICLE PAGE
            |--------------------------------------------------------------------------
            */
            if ($article && !$category) {

                // 🔐 Chargement optimisé (pas de requêtes répétées)
                $articleId = $article->id;

                $files = Publication::select('files.file_url')
                    ->leftJoin('publication_files', 'publication_files.publication_id', '=', 'publications.id')
                    ->leftJoin('files', 'files.id', '=', 'publication_files.file_id')
                    ->where('publications.id', $articleId)
                    ->get();

                $tags = PublicationTag::select('tags.name', 'tags.id', 'tags.slug')
                    ->leftJoin('publications', 'publications.id', '=', 'publication_tags.publication_id')
                    ->leftJoin('tags', 'tags.id', '=', 'publication_tags.tag_id')
                    ->where('publications.id', $articleId)
                    ->get();

                $tagsCount = PublicationTag::where('publication_id', $articleId)->count();

                $previous = Publication::where('id', '<', $articleId)
                    ->where('status', 1)
                    ->where('type_publication_id', 1)
                    ->where('deja_citer', 0)
                    ->orderBy('date_publish', 'desc')
                    ->first();

                $next = Publication::where('id', '>', $articleId)
                    ->where('status', 1)
                    ->where('type_publication_id', 1)
                    ->where('deja_citer', 0)
                    ->orderBy('date_publish', 'desc')
                    ->first();

                $excludeIds = [$articleId];

                if ($previous) $excludeIds[] = $previous->id;
                if ($next) $excludeIds[] = $next->id;

                $similars = Publication::where('status', 1)
                    ->where('type_publication_id', 1)
                    ->where('category_id', $article->category_id)
                    ->where('deja_citer', 0)
                    ->whereNotIn('id', $excludeIds)
                    ->orderBy('date_publish', 'desc')
                    ->take(9)
                    ->get();

                $alireaussi = Publication::where('status', 1)
                    ->where('type_publication_id', 1)
                    ->where('deja_citer', 0)
                    ->orderBy('date_publish', 'desc')
                    ->take(5)
                    ->get();

                $categoriesH = Publication::where("publications.id", $articleId)->get();

                return view('oneSlugPage.publication', [
                    'article' => $article,
                    'files' => $files,
                    'tags' => $tags,
                    'tagsCount' => $tagsCount,
                    'previous' => $previous,
                    'next' => $next,
                    'similars' => $similars,
                    'alireaussi' => $alireaussi,
                    'categoriesH' => $categoriesH
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | ❌ FALLBACK
            |--------------------------------------------------------------------------
            */
            return view('errors.ErrorSlugPage');
        }
    }

    public function tags($slug)
    {
        // 🔐 sécurisation slug
        $slug = trim(strip_tags($slug));

        $tag = Tag::where('slug', $slug)->first();

        if (!$tag) {
            return view('errors.ErrorSlugPage');
        }

        $articles = Publication::select(
                'publications.id',
                'publications.content',
                'publications.truncate_content',
                'publications.title',
                'publications.slug',
                'publications.date_publish',
                'publications.author_name',
                'publications.author_slug',
                'publications.image_cover_url'
            )
            ->where('publications.status', 1)
            ->where('publications.type_publication_id', 1)
            ->where('publications.deja_citer', 0)
            ->whereHas('tags', function($q) use ($tag) {
                $q->where('tags.id', $tag->id);
            })
            ->orderBy('publications.date_publish', 'desc')
            ->paginate(6);

        $otherTags = Tag::all();

        $alireaussi = Publication::where('status', 1)
            ->where('type_publication_id', 1)
            ->where('deja_citer', 0)
            ->orderBy('date_publish', 'desc')
            ->take(5)
            ->get();

        return view('oneSlugPage.tags', [
            'articles' => $articles,
            'tag' => $tag,
            'alireaussi' => $alireaussi,
            'otherCategory' => $otherTags
        ]);
    }

    public function authors($slug)
    {
        // 🔐 sécurisation slug
        $slug = trim(strip_tags($slug));

        $author = Author::where('slug', $slug)->first();

        if (!$author) {
            return view('errors.ErrorSlugPage');
        }

        $articles = Publication::where('status', 1)
            ->where('type_publication_id', 1)
            ->where('deja_citer', 0)
            ->where('author_slug', $author->slug)
            ->orderBy('date_publish', 'desc')
            ->paginate(6);

        $otherCategory = Category::all();

        $alireaussi = Publication::where('status', 1)
            ->where('type_publication_id', 1)
            ->where('deja_citer', 0)
            ->orderBy('date_publish', 'desc')
            ->take(5)
            ->get();

        return view('oneSlugPage.authors', [
            'author' => $author,
            'articles' => $articles,
            'alireaussi' => $alireaussi,
            'otherCategory' => $otherCategory
        ]);
    }
}
