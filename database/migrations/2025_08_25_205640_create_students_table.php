<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('registration_no')->nullable()->unique();
            $table->string('admission_no')->nullable()->unique();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->text('health_info')->nullable();
            $table->text('family_info')->nullable();
            $table->text('transfer_history')->nullable();
            $table->text('immunization')->nullable();
            $table->text('immunization_record')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->enum('status', ['active','pending','transfer'])->default('active');
            $table->enum('registration_status', ['pending','approved','rejected'])->default('pending');
            $table->date('enrollment_date')->nullable();
            $table->timestamps();

            $table->index(['school_id','class_id','section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
