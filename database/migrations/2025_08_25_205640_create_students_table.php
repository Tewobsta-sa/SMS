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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('registration_no')->nullable();
            $table->string('admission_no')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->text('health_info')->nullable();
            $table->text('family_info')->nullable();
            $table->text('transfer_history')->nullable();
            $table->text('immunization')->nullable();
            $table->text('immunization_record')->nullable();
            $table->foreignId('guardian_id')->nullable()->constrained('parent_models')->onDelete('set null');
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->onDelete('set null');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->enum('status', ['active','pending','transfer'])->default('active');
            $table->enum('registration_status', ['pending','approved','rejected'])->default('pending');
            $table->date('enrollment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
