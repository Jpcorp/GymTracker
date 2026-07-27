<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->date('photo_date');
            $table->enum('view_type', ['front', 'back', 'left_side', 'right_side']);
            $table->string('photo_path', 255);
            $table->foreignId('evaluation_id')->nullable()->constrained();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_photos');
    }
};
