<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offres', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mission_id')
                ->constrained('missions')
                ->cascadeOnDelete();

            $table->foreignId('prestataire_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('prix_propose', 10, 2);

            $table->text('message')->nullable();

            $table->enum('statut', [
                'en_attente',
                'acceptee',
                'refusee',
            ])->default('en_attente');

            $table->timestamps();

            $table->unique(['mission_id', 'prestataire_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offres');
    }
};
