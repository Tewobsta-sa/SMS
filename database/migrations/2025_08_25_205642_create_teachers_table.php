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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('employee_no')->nullable();
            $table->string('employee_number')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('department')->nullable();
            $table->string('specialization')->nullable();
            $table->string('qualification')->nullable();
            $table->string('qualifications')->nullable();
            $table->integer('workload')->nullable();
            $table->integer('workload_hours')->nullable();
            $table->integer('experience_years')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
