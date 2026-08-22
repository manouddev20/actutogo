<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::where(
            'slug',
            'administrateur'
        )->first();

        $publisherRole = Role::where(
            'slug',
            'publicateur-darticles'
        )->first();

        if (!$adminRole || !$publisherRole) {
            $this->command->error(
                '❌ Les rôles nécessaires sont introuvables. '
                . 'Exécutez d\'abord RoleTableSeeder.'
            );

            return;
        }

        $users = [
            [
                'name' => 'Alexandre DENANYOH',
                'username' => 'MawuwonamTG',
                'email' => 'nonojack@yahoo.fr',
                'password' => '040567Ionos',
                'role_id' => $adminRole->id,
            ],

            [
                'name' => 'Eric MIKANDO',
                'username' => 'delomepub',
                'email' => 'ericmakondo@gmail.com',
                'password' => 'delomepub92',
                'role_id' => $publisherRole->id,
            ],
        ];

        $total = count($users);
        $inserted = 0;

        foreach ($users as $data) {

            $user = User::updateOrCreate(
                [
                    'email' => $data['email'],
                ],
                [
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'role_id' => $data['role_id'],
                    'password' => Hash::make($data['password']),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $inserted++;
            }

            $this->command->info(
                "Utilisateur '{$user->username}' : "
                . (
                    $user->wasRecentlyCreated
                        ? 'créé'
                        : 'déjà existant / mis à jour'
                )
            );
        }

        $this->command->info(
            "✅ Import terminé : "
            . "{$total} utilisateurs traités, "
            . "{$inserted} créés."
        );
    }
}