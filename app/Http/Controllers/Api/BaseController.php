<?php

// Déclaration de l'espace de nom (namespace) du contrôleur
namespace App\Http\Controllers\Api;

// Importation du contrôleur de base de Laravel
use App\Http\Controllers\Controller;

// Importation de la classe JsonResponse pour typer les réponses JSON
use Illuminate\Http\JsonResponse;

// Définition de la classe BaseController qui étend le contrôleur principal
class BaseController extends Controller
{
    /**
     * Méthode pour retourner une réponse de succès standardisée
     *
     * @param mixed $result  Données à retourner (peut être un tableau, objet, etc.)
     * @param string $message Message descriptif du succès
     * @return JsonResponse  Réponse JSON avec statut HTTP 200
     */
    public function sendResponse($result, $message): JsonResponse
    {
        // Retourne une réponse JSON structurée pour les succès
        return response()->json([
            'success' => true,      // Indique que la requête est réussie
            'data' => $result,      // Contient les données retournées
            'message' => $message,  // Message associé à la réponse
        ], 200); // Code HTTP 200 = OK
    }

    /**
     * Méthode pour retourner une réponse d'erreur standardisée
     *
     * @param string $message Message principal de l'erreur
     * @param array $errors   Détails supplémentaires des erreurs (optionnel)
     * @param int $code       Code HTTP de la réponse (par défaut 400)
     * @return JsonResponse   Réponse JSON avec code HTTP personnalisé
     */
    public function sendError($message, $errors = [], $code = 400): JsonResponse
    {
        // Structure de base de la réponse d'erreur
        $response = [
            'success' => false, // Indique que la requête a échoué
            'message' => $message, // Message principal d'erreur
        ];

        // Vérifie s'il existe des erreurs supplémentaires à ajouter
        if (!empty($errors)) {
            $response['errors'] = $errors; // Ajoute les détails des erreurs
        }

        // Retourne la réponse JSON avec le code HTTP spécifié
        return response()->json($response, $code);
    }
}