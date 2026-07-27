<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->date('recorded_at');
            $table->decimal('weight_kg', 5, 2);
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('body_fat_percentage', 5, 2)->nullable();
            $table->decimal('bmi', 4, 2)->nullable();
            $table->integer('metabolic_age')->nullable();
            $table->integer('basal_kcal')->nullable();
            $table->integer('visceral_fat')->nullable();
            $table->foreignId('evaluation_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_metrics');
    }
};
