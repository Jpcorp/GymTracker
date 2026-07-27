<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->date('workout_date');
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->integer('completed_sets')->nullable();
            $table->string('completed_reps', 50)->nullable();
            $table->integer('rpe')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_logs');
    }
};
