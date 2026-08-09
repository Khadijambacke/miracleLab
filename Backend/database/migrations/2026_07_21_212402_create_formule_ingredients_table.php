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
        Schema::create('formule_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formule_id')->constrained('formules')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->decimal('pourcentage', 5, 2);
            $table->decimal('cout_par_kg', 10, 2)->default(0.00);
            $table->decimal('grammes_calculs', 8, 2);
            $table->string('phase'); // AQUEUSE, HUILEUSE, REFROIDISSEMENT
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formule_ingredients');
    }
};
