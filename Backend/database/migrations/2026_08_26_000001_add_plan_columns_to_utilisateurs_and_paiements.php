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
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->string('type_plan')->nullable()->after('statut_abonnement');
            $table->timestamp('date_expiration_abonnement')->nullable()->after('type_plan');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->string('type_plan')->nullable()->after('montant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropColumn(['type_plan', 'date_expiration_abonnement']);
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn(['type_plan']);
        });
    }
};
