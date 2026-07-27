<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('satisfaction_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->date('survey_date');
            $table->integer('overall_satisfaction');
            $table->integer('trainer_satisfaction')->nullable();
            $table->integer('facilities_satisfaction')->nullable();
            $table->integer('routines_satisfaction')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satisfaction_surveys');
    }
};
