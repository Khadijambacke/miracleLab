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
        Schema::create('types_produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->nullable()->constrained('utilisateurs')->onDelete('cascade');
            $table->string('nom'); // ex: Leave-in Spray, Shampoing Doux, Crème Visage
            $table->string('code')->unique(); // ex: LEAVE_IN, SHAMPOO_CUSTOM
            $table->string('categorie'); // haircare, skincare
            $table->json('squelette')->nullable(); // Ingrédients et pourcentages par défaut
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types_produits');
    }
};
