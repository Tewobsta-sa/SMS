<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('employee_no')->nullable()->unique();
            $table->date('hire_date')->nullable();
            $table->string('department')->nullable()->index();
            $table->string('specialization')->nullable();
            $table->text('qualifications')->nullable(); // freeform JSON/text
            $table->integer('workload')->nullable();
            $table->integer('workload_hours')->nullable();
            $table->integer('experience_years')->nullable();
            $table->timestamps();

            $table->unique(['school_id','user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
