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
            $table->enum('goal_metric', ['weight_kg', 'body_fat_percentage'])->nullable();
            $table->decimal('goal_target_value', 6, 2)->nullable();
            $table->date('goal_target_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['goal_metric', 'goal_target_value', 'goal_target_date']);
        });
    }
};
