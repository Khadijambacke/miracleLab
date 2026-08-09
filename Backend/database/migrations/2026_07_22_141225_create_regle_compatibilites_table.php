<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regles_compatibilite', function (Blueprint $table) {
            $table->id();
            $table->string('nom_regle');
            $table->json('groupe_a');
            $table->json('groupe_b');
            $table->enum('niveau', ['warn', 'error'])->default('warn');
            $table->text('message_alerte');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regles_compatibilite');
    }
};
