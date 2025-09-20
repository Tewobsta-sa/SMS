<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('occupation')->nullable();
            $table->enum('relation', ['Father','Mother','Guardian'])->nullable();
            $table->timestamps();

            $table->unique(['school_id','user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
