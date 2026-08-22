<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
         * 1. Rôles
         *
         * La table users dépend de role_id.
         */
        $this->call(RoleTableSeeder::class);

        /*
         * 2. Utilisateurs
         *
         * Les autres tables administrables
         * dépendent généralement de user_id.
         */
        $this->call(UserTableSeeder::class);

        /*
         * 3. Newsletter
         *
         * Indépendante des autres tables.
         */
        $this->call(NewsLetterTableSeeder::class);

        /*
         * 4. Auteurs
         *
         * Dépend de users via user_id.
         */
        $this->call(AuthorTableSeeder::class);

        /*
         * 5. Types de publication
         *
         * Dépend de users via user_id.
         */
        $this->call(TypePublicationTableSeeder::class);

        /*
         * 6. Types de fichiers
         *
         * Dépend de users via user_id.
         */
        $this->call(FileTypeTableSeeder::class);

        /*
         * 7. Catégories
         *
         * Dépend de users via user_id.
         */
        $this->call(CategoryTableSeeder::class);

        /*
         * 8. Tags
         *
         * Dépend de users via user_id.
         */
        $this->call(TagsTableSeeder::class);

        /*
         * 9. Fichiers
         *
         * Dépend de :
         * - users
         * - type_files
         */
        $this->call(FileTableSeeder::class);

        /*
         * 10. Publications
         *
         * Dépend notamment de :
         * - users
         * - authors
         * - type_publications
         * - categories
         * - tags
         * - files
         */
        $this->call(PublicationTableSeeder::class);

        /*
         * 11. Commentaires
         *
         * Dépend des publications.
         */
        $this->call(CommentsTableSeeder::class);

        /*
         * 12. Génération des sitemaps
         *
         * À faire après l'import des données
         * pour générer les URLs à partir des
         * auteurs, catégories et tags.
         
        $this->call(GenerateSitemapSeeder::class);

        */
    }
}