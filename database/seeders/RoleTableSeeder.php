<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => "Administrateur",
                'nbRsp' => '&nbtsd!?'
            ],
            [
                'name' => "Publicateur d' articles",
                'nbRsp' => '&nbrsp?!'
            ],
            [
                'name' => "Visiteur",
                'nbRsp' => '&nbdfpo@!'
            ]
        ];

        $totalFetched = count($roles);
        $totalInserted = 0;

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['slug' => Str::slug($roleData['name'])], // clé unique
                [
                    'name' => $roleData['name'],
                    'nbRsp' => $roleData['nbRsp']
                ]
            );

            if ($role->wasRecentlyCreated) {
                $totalInserted++;
            }

            $this->command->info("Rôle '{$role->name}' traité : " . ($role->wasRecentlyCreated ? 'nouveau' : 'existant'));
        }

        $this->command->info("✅ Import des rôles terminé : $totalFetched récupérés, $totalInserted insérés en base.");
    }
}
