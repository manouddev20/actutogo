<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Début du transfert des utilisateurs...');

        $users = [
            [
                'role' => 'administrateur',
                'name' => 'Administrateur',
                'username' => 'administrateur',
                'nom' => 'Administrateur',
                'prenoms' => 'Principal',
                'nom_complet' => 'Administrateur Principal',
                'email' => 'admin@actualitetogo.com',
                'telephone' => '+22890000000',
                'password' => 'password',
            ],
            [
                'role' => 'visiteur',
                'name' => 'Visiteur',
                'username' => 'visiteur',
                'nom' => 'Visiteur',
                'prenoms' => 'Principal',
                'nom_complet' => 'Visiteur Principal',
                'email' => 'visiteur@actualitetogo.com',
                'telephone' => '+22890000001',
                'password' => 'password',
            ],
            [
                'role' => 'publicateur-articles',
                'name' => 'Publicateur',
                'username' => 'publicateur',
                'nom' => 'Publicateur',
                'prenoms' => 'Articles',
                'nom_complet' => 'Publicateur Articles',
                'email' => 'publicateur@actualitetogo.com',
                'telephone' => '+22890000002',
                'password' => 'password',
            ],
        ];

        $transferred = 0;

        foreach ($users as $userData) {
            $role = Role::where('slug', $userData['role'])->firstOrFail();

            User::updateOrCreate(
                [
                    'email' => $userData['email'],
                ],
                [
                    'role_id' => $role->id,
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'nom' => $userData['nom'],
                    'prenoms' => $userData['prenoms'],
                    'nom_complet' => $userData['nom_complet'],
                    'telephone' => $userData['telephone'],
                    'password' => Hash::make($userData['password']),
                    'status' => true,
                ]
            );

            $transferred++;

            $this->command->info(
                'Utilisateur transféré : ' .
                $transferred .
                ' / ' .
                count($users)
            );
        }

        $this->command->info(
            'Transfert des utilisateurs terminé : ' .
            $transferred .
            ' utilisateurs.'
        );
    }
}