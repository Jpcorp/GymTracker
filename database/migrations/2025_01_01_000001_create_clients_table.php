<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('phone', 20)->nullable();
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('start_date');
            $table->text('goal')->nullable();
            $table->string('profile_photo')->nullable();
            $table->enum('status', ['active', 'inactive', 'paused'])->default('active');
            $table->foreignId('trainer_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
