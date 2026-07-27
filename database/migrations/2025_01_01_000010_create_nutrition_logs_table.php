<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrition_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->date('log_date');
            $table->enum('compliance', ['complete', 'partial', 'missed'])->default('complete');
            $table->integer('meals_logged')->default(0);
            $table->integer('meals_planned')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_logs');
    }
};
