<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuthorTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Début du transfert des auteurs...');

        $user = User::whereHas('role', function ($query) {
            $query->where('slug', 'administrateur');
        })->first();

        if (!$user) {
            $this->command->error(
                'Aucun utilisateur administrateur trouvé. Le transfert des auteurs est annulé.'
            );

            return;
        }

        $authors = [
            [
                'nom' => 'Kossi',
                'prenom' => 'Emmanuel',
                'nom_complet' => 'Kossi Emmanuel',
                'nom_auteur' => 'Togonews',
                'telephone' => null,
                'adresse' => null,
                'description' => 'Auteur et contributeur du site.',
                'status' => true,
                'wp_author_id' => 1,
            ],
            [
                'nom' => 'Jean',
                'prenom' => 'Koffi',
                'nom_complet' => 'deltanews',
                'nom_auteur' => 'deltaweb',
                'telephone' => null,
                'adresse' => null,
                'description' => 'Auteur et contributeur du site.',
                'status' => true,
                'wp_author_id' => 10,
            ],
        ];

        $transferred = 0;

        foreach ($authors as $authorData) {
            Author::updateOrCreate(
                ['nom_auteur' => $authorData['nom_auteur']],
                [
                    'created_by' => $user->id,
                    'user_id' => null,
                    'nom' => $authorData['nom'],
                    'prenom' => $authorData['prenom'],
                    'nom_complet' => $authorData['nom_complet'],
                    'slug' => Str::slug($authorData['nom_auteur']),
                    'nom_auteur' => $authorData['nom_auteur'],
                    'telephone' => $authorData['telephone'],
                    'adresse' => $authorData['adresse'],
                    'description' => $authorData['description'],
                    'status' => $authorData['status'],
                    'wp_author_id' => $authorData['wp_author_id'],
                ]
            );

            $author = Author::where(
                'nom_auteur',
                $authorData['nom_auteur']
            )->first();

            if ($author && empty($author->slug)) {
                $author->update([
                    'slug' => Str::slug($authorData['nom_auteur']),
                ]);
            }

            $transferred++;

            $this->command->info(
                'Auteur transféré : ' .
                $transferred .
                ' / ' .
                count($authors)
            );
        }

        $this->command->info(
            'Transfert des auteurs terminé : ' .
            $transferred .
            ' auteurs.'
        );
    }
}