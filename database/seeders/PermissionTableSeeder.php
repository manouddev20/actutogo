<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Début du transfert des permissions et des liaisons...');

        $permissions = [
            [
                'name' => 'Lire les publications',
                'slug' => 'publications.lire',
                'description' => 'Permet de consulter les publications',
            ],
            [
                'name' => 'Aimer une publication',
                'slug' => 'publications.aimer',
                'description' => 'Permet d’aimer une publication',
            ],
            [
                'name' => 'Commenter une publication',
                'slug' => 'commentaires.creer',
                'description' => 'Permet de publier un commentaire',
            ],
            [
                'name' => 'Signaler un commentaire',
                'slug' => 'commentaires.signaler',
                'description' => 'Permet de signaler un commentaire',
            ],
            [
                'name' => 'Aimer un commentaire',
                'slug' => 'commentaires.aimer',
                'description' => 'Permet d’aimer un commentaire',
            ],
            [
                'name' => 'Répondre à un commentaire',
                'slug' => 'commentaires.repondre',
                'description' => 'Permet de répondre à un commentaire',
            ],
            [
                'name' => 'Gérer les likes',
                'slug' => 'likes.gerer',
                'description' => 'Permet de gérer les likes',
            ],
            [
                'name' => 'Lire les publications',
                'slug' => 'publications.lire',
                'description' => 'Permet de consulter les publications',
            ],
            [
                'name' => 'Ajouter une publication',
                'slug' => 'publications.ajouter',
                'description' => 'Permet d’ajouter une publication',
            ],
            [
                'name' => 'Modifier une publication',
                'slug' => 'publications.modifier',
                'description' => 'Permet de modifier une publication',
            ],
            [
                'name' => 'Supprimer une publication',
                'slug' => 'publications.supprimer',
                'description' => 'Permet de supprimer une publication',
            ],
            [
                'name' => 'Publier un article',
                'slug' => 'publications.publier',
                'description' => 'Permet de publier un article',
            ],
            [
                'name' => 'Gérer les publications',
                'slug' => 'publications.gerer',
                'description' => 'Permet de gérer les publications',
            ],
            [
                'name' => 'Gérer les tags',
                'slug' => 'tags.gerer',
                'description' => 'Permet de gérer les tags',
            ],
            [
                'name' => 'Gérer les auteurs',
                'slug' => 'auteurs.gerer',
                'description' => 'Permet de gérer les auteurs',
            ],
            [
                'name' => 'Gérer les fichiers',
                'slug' => 'fichiers.gerer',
                'description' => 'Permet de gérer les fichiers',
            ],
            [
                'name' => 'Gérer les catégories',
                'slug' => 'categories.gerer',
                'description' => 'Permet de gérer les catégories',
            ],
            [
                'name' => 'Gérer les types de publications',
                'slug' => 'types-publications.gerer',
                'description' => 'Permet de gérer les types de publications',
            ],
            [
                'name' => 'Gérer les messages',
                'slug' => 'messages.gerer',
                'description' => 'Permet de gérer les messages de contact',
            ],
            [
                'name' => 'Gérer les utilisateurs',
                'slug' => 'utilisateurs.gerer',
                'description' => 'Permet de gérer les utilisateurs',
            ],
        ];

        DB::transaction(function () use ($permissions) {
            foreach ($permissions as $permission) {
                Permission::updateOrCreate(
                    ['slug' => $permission['slug']],
                    [
                        'name' => $permission['name'],
                        'description' => $permission['description'],
                        'status' => true,
                    ]
                );
            }
        });

        $this->command->info('Permissions transférées : ' . count($permissions));

        $this->command->info('Création des liaisons entre les rôles et les permissions...');

        $visiteurPermissions = [
            'publications.lire',
            'publications.aimer',
            'commentaires.creer',
            'commentaires.signaler',
            'commentaires.aimer',
            'commentaires.repondre',
        ];

        $publicateurPermissions = [
            ...$visiteurPermissions,
            'publications.ajouter',
            'publications.modifier',
            'publications.supprimer',
            'publications.publier',
            'publications.gerer',
            'tags.gerer',
            'auteurs.gerer',
            'fichiers.gerer',
        ];

        $administrateurPermissions = [
            ...$publicateurPermissions,
            'categories.gerer',
            'types-publications.gerer',
            'messages.gerer',
            'utilisateurs.gerer',
            'likes.gerer',
        ];

        $permissionIds = Permission::whereIn(
            'slug',
            array_unique($administrateurPermissions)
        )->pluck('id', 'slug');

        $roles = [
            'visiteur' => $visiteurPermissions,
            'publicateur-articles' => $publicateurPermissions,
            'administrateur' => $administrateurPermissions,
        ];

        $totalLinks = 0;

        DB::transaction(function () use ($roles, $permissionIds, &$totalLinks) {
            foreach ($roles as $roleSlug => $permissionSlugs) {
                $role = Role::where('slug', $roleSlug)->firstOrFail();

                $ids = collect($permissionSlugs)
                    ->unique()
                    ->map(fn ($slug) => $permissionIds->get($slug))
                    ->filter()
                    ->values()
                    ->toArray();

                $role->permissions()->sync($ids);

                $totalLinks += count($ids);

                $this->command->info(
                    'Liaisons transférées pour ' .
                    $role->name .
                    ' : ' .
                    count($ids)
                );
            }
        });

        $this->command->info('Total des liaisons transférées : ' . $totalLinks);
        $this->command->info('Transfert des permissions terminé.');
    }
}