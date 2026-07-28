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
        Schema::table('routines', function (Blueprint $table) {
            // Periodization phase (Issurin/block-periodization terminology): accumulation (base volume),
            // intensification (strength focus), realization (peaking), deload (planned unloading week).
            $table->enum('phase', ['accumulation', 'intensification', 'realization', 'deload'])
                ->default('accumulation')
                ->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            $table->dropColumn('phase');
        });
    }
};
