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
        Schema::table('paiements', function (Blueprint $table) {
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->decimal('montant', 10, 2);
            $table->string('statut')->default('REUSSI');
            $table->string('methode_paiement')->nullable();
            $table->string('reference_transaction')->nullable();
            $table->timestamp('date_paiement')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['utilisateur_id']);
            $table->dropColumn(['utilisateur_id', 'montant', 'statut', 'methode_paiement', 'reference_transaction', 'date_paiement']);
        });
    }
};
