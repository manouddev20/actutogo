<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();

            /*
             * Nullable car lors de l'inscription,
             * l'utilisateur peut ne pas encore être enregistré.
             */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Email auquel l'OTP est envoyé.
             */
            $table->string('email')->index();

            /*
             * Code OTP.
             * Exemple : 123456
             */
            $table->string('otp', 255);

            /*
             * Type d'OTP :
             * register
             * login
             * reset_password
             */
            $table->enum('type', [
                'register',
                'login',
                'reset_password',
            ]);

            /*
             * Statut :
             * 0 = en attente
             * 1 = vérifié
             * 2 = expiré
             */
            $table->tinyInteger('status')->default(0);

            /*
             * Nombre de tentatives de vérification.
             */
            $table->unsignedTinyInteger('attempts')->default(0);

            /*
             * Date d'expiration de l'OTP.
             */
            $table->timestamp('expires_at');

            /*
             * Date à laquelle l'OTP a été validé.
             */
            $table->timestamp('verified_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            /*
             * Index pour accélérer les recherches.
             */
            $table->index([
                'email',
                'type',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};