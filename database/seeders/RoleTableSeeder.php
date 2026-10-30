<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrateur',
                'slug' => 'administrateur',
                'description' => 'Administrateur du site',
                'status' => true,
            ],
            [
                'name' => 'Publicateur d’articles',
                'slug' => 'publicateur-articles',
                'description' => 'Utilisateur chargé de publier et gérer les articles',
                'status' => true,
            ],
            [
                'name' => 'Visiteur',
                'slug' => 'visiteur',
                'description' => 'Utilisateur disposant des accès réservés aux visiteurs',
                'status' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'status' => $role['status'],
                ]
            );
        }

        $this->command->info('3 rôles transférés avec succès.');
    }
}