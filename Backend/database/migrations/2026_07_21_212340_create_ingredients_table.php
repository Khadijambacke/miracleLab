<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->nullable()->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('categorie_id')->nullable()->constrained('categories_ingredients')->onDelete('set null');
            $table->string('nom');
            $table->string('phase'); // AQUEUSE, HUILEUSE, REFROIDISSEMENT
            $table->string('nom_groupe');
            $table->text('note')->nullable();
            $table->decimal('pourcentage_min', 5, 2)->nullable();
            $table->decimal('pourcentage_max', 5, 2)->nullable();
            $table->decimal('impact_ph', 3, 1)->default(0.0);
            $table->boolean('est_personnalise')->default(false);
            
            // Fiche technique (champs optionnels directs pour accès rapide)
            $table->string('inci')->nullable();
            $table->string('solubilite')->nullable();
            $table->text('precautions')->nullable();
            $table->text('conseils')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
