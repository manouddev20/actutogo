<?php

// Déclaration du namespace correspondant à l'organisation des contrôleurs API Frontoffice
namespace App\Http\Controllers\Api\Web\Frontoffice;

// Importation du contrôleur de base personnalisé
use App\Http\Controllers\Api\BaseController;

// Importation des modèles nécessaires
use App\Models\Category;
use App\Models\Publication;
use App\Models\Tag;

// Définition du contrôleur HomeController qui hérite de BaseController
class HomeController extends BaseController
{
   /**
    * Méthode principale pour afficher la page d'accueil
    *
    * @return \Illuminate\View\View
    */
   public function home()
   {
        // Vérifie s'il existe au moins une publication active (status = 1)
        // Utilisation de exists() pour optimiser les performances (plus rapide que count())
        if (!Publication::where('status', 1)->exists()) {
            // Si aucune publication n'existe, retourner une vue d'erreur personnalisée
            return view('errors.HomePageControlEmpty');
        }

        // Récupération des publications "À la une"
        $alaUne = Publication::where([
                ['status', 1],                  // Publication active
                ['type_publication_id', 1],     // Type "À la une"
                ['deja_citer', 0],              // Non encore cité
            ])
            ->latest('date_publish') // Trie les publications par date de publication décroissante
            ->take(13)               // Limite le nombre de résultats à 13
            ->get();                 // Exécute la requête et récupère les résultats

        // Retourne la vue 'welcome' avec les données des publications "À la une"
        return view('welcome', compact('alaUne'));
   }
}