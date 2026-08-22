<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrateur',
            ],
            [
                'name' => 'Publicateur d\'articles',
            ],
            [
                'name' => 'Visiteur',
            ],
        ];

        $total = count($roles);
        $inserted = 0;

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                [
                    'slug' => Str::slug($roleData['name']),
                ],
                [
                    'name' => $roleData['name'],
                    'slug' => Str::slug($roleData['name']),
                ]
            );

            if ($role->wasRecentlyCreated) {
                $inserted++;
            }

            $this->command->info(
                "Rôle '{$role->name}' : "
                . ($role->wasRecentlyCreated ? 'créé' : 'déjà existant')
            );
        }

        $this->command->info(
            "✅ Import terminé : {$total} rôles traités, {$inserted} créés."
        );
    }
}