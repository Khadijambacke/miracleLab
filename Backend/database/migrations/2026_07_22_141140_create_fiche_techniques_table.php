<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiches_techniques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->unique()->constrained('ingredients')->onDelete('cascade');
            $table->string('nom_inci')->nullable();
            $table->string('categorie_fonctionnelle')->nullable();
            $table->string('solubilite')->nullable();
            $table->string('temperature_incorporation')->nullable();
            $table->decimal('ph_optimal_min', 3, 1)->nullable();
            $table->decimal('ph_optimal_max', 3, 1)->nullable();
            $table->text('precautions')->nullable();
            $table->text('conseils_formulateur')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiches_techniques');
    }
};
