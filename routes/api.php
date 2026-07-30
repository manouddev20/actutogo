<?php 
use App\Http\Controllers\Api\Web\Frontoffice\IncludesController;   

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Frontoffice Includes
|--------------------------------------------------------------------------
|
| Routes pour récupérer les données dynamiques du frontoffice :
| - Header (menu, catégories, rubriques)
| - Footer (newsletter, tags, catégories, articles populaires)
|
*/

/*
|--------------------------------------------------------------------------
| HEADER ROUTES
|--------------------------------------------------------------------------
| Toutes les routes liées au header sont regroupées avec un préfixe
| pour éviter la répétition de /frontoffice/header
|
*/

Route::prefix('frontoffice/header')->group(function () {
    // Actualités principales du Togo
    Route::get('/economie', [IncludesController::class, 'economieRequestData']); 
    // Rubriques principales
    Route::get('/rubriques', [IncludesController::class, 'rubriquesRequestData']);
    // Sections spécifiques
    Route::get('/international', [IncludesController::class, 'internationalRequestData']);
    Route::get('/politique', [IncludesController::class, 'politiqueRequestData']);
    Route::get('/societe', [IncludesController::class, 'societeRequestData']);
    Route::get('/diaspora', [IncludesController::class, 'diasporaRequestData']);
    Route::get('/rubriques', [IncludesController::class, 'rubriquesRequestData']);
});


/*
|--------------------------------------------------------------------------
| FOOTER ROUTES
|--------------------------------------------------------------------------
| Regroupement des routes liées au footer :
| newsletter + contenus populaires
|
*/

Route::prefix('frontoffice/footer')->group(function () {
    // Inscription à la newsletter
    Route::post('/newsletter', [IncludesController::class, 'newsletterStoreRequest']);
    // Tags populaires
    Route::get('/tags_populars', [IncludesController::class, 'tagsRequestData']);
    // Catégories populaires
    Route::get('/category_populars', [IncludesController::class, 'categoryRequestData']);
    // Articles populaires
    Route::get('/articles_populars', [IncludesController::class, 'publicationsRequestData']);
});
