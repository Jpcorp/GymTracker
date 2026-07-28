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
        Schema::table('clients', function (Blueprint $table) {
            $table->integer('nutrition_target_kcal')->nullable();
            $table->integer('nutrition_target_protein_g')->nullable();
            $table->string('nutrition_target_notes', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['nutrition_target_kcal', 'nutrition_target_protein_g', 'nutrition_target_notes']);
        });
    }
};
