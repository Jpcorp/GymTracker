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
        Schema::table('mood_records', function (Blueprint $table) {
            $table->decimal('sleep_hours', 4, 1)->nullable();
            $table->integer('sleep_quality')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mood_records', function (Blueprint $table) {
            $table->dropColumn(['sleep_hours', 'sleep_quality']);
        });
    }
};
