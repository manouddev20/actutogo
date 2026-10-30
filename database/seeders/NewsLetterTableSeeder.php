<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class NewsletterSeeder extends Seeder
{
    public function run(): void
    {
        $total = 100;
        $batchSize = 10;
        $faker = Faker::create();
        $transferred = 0;

        $this->command->info('Début du transfert de ' . number_format($total, 0, ',', ' ') . ' abonnés newsletter...');

        for ($offset = 0; $offset < $total; $offset += $batchSize) {
            $batch = [];

            $currentBatchSize = min($batchSize, $total - $offset);

            for ($i = 0; $i < $currentBatchSize; $i++) {
                $batch[] = [
                    'user_id' => null,
                    'email' => $faker->unique()->safeEmail(),
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('newsletters')->insert($batch);

            $transferred += $currentBatchSize;

            $this->command->info(
                'Transfert newsletter : ' .
                number_format($transferred, 0, ',', ' ') .
                ' / ' .
                number_format($total, 0, ',', ' ')
            );
        }

        $this->command->info('Transfert newsletter terminé : ' . number_format($transferred, 0, ',', ' ') . ' abonnés.');
    }
}