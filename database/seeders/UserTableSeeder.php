<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'nom' => 'DENANYOH',
                'nom_complet' => 'Alexandre DENANYOH',
                'prenoms' => 'Alexandre',
                'telephone' => '+33 6 27 38 75 14',
                'author_id' => 1,
                'username' => 'MawuwonamTG',
                'role_id' => 1,
                'status' => 1,
                'email' => 'nonojack@yahoo.fr',
                'password' => '040567Ionos', // le mot de passe sera hashé ci-dessous
            ],
            [
                'nom' => 'MIKANDO',
                'prenoms' => 'Eric',
                'nom_complet' => 'Eric MIKANDO',
                'telephone' => '+228 91 91 91 91',
                'author_id' => 2,
                'username' => 'delomepub',
                'role_id' => 2,
                'status' => 1,
                'email' => 'ericmakondo@gmail.com',
                'password' => 'delomepub92', // le mot de passe sera hashé ci-dessous
            ],
        ];

        $totalFetched = count($users);
        $totalInserted = 0;

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']], // clé unique
                [
                    'nom' => $data['nom'],
                    'prenoms' => $data['prenoms'],
                    'nom_complet' => $data['nom_complet'],
                    'telephone' => $data['telephone'],
                    'author_id' => $data['author_id'],
                    'username' => $data['username'],
                    'slug' => Str::slug($data['username']),
                    'role_id' => $data['role_id'],
                    'status' => $data['status'],
                    'password' => Hash::make($data['password']),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $totalInserted++;
            }

            $this->command->info("Utilisateur '{$user->username}' traité : " . ($user->wasRecentlyCreated ? 'nouveau' : 'existant'));
        }

        $this->command->info("✅ Import des utilisateurs terminé : $totalFetched récupérés, $totalInserted insérés en base.");
    }
}
