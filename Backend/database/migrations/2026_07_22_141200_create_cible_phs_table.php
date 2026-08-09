<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cibles_ph', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_produit_id')->constrained('types_produits')->onDelete('cascade');
            $table->decimal('ph_min', 3, 1);
            $table->decimal('ph_max', 3, 1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cibles_ph');
    }
};
