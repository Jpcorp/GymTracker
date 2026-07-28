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
        Schema::create('injuries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('body_part', 100);
            $table->date('reported_date');
            $table->integer('severity'); // 1-10 pain/severity scale, same convention as mood/rpe elsewhere in this app
            $table->enum('status', ['active', 'recovering', 'resolved'])->default('active');
            $table->text('notes')->nullable();
            $table->date('resolved_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('injuries');
    }
};
