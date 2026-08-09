<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('squelettes_compositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_produit_id')->constrained('types_produits')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->string('phase'); // AQUEUSE, HUILEUSE, REFROIDISSEMENT, etc.
            $table->decimal('pourcentage_defaut', 5, 2)->default(0.00);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('squelettes_compositions');
    }
};
