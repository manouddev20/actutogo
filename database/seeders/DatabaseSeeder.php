<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleTableSeeder::class,
            PermissionTableSeeder::class,
            UserTableSeeder::class,
            TypePublicationTableSeeder::class,
            AuthorTableSeeder::class,
            CategoryTableSeeder::class,
            TagTableSeeder::class,
            TypeMediaFileTableSeeder::class,
            MediaFileTableSeeder::class,
            PublicationTableSeeder::class,
        ]);
    }
}
