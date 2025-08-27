<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key for Multi-tenancy
            $table->foreignId('school_id')
                  ->constrained()
                  ->onDelete('cascade');

            // User Profile Information
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('profile_picture_url')->nullable();

            // Authentication & Security
            $table->string('password');
            $table->string('role'); // e.g., 'Admin', 'Teacher', 'Parent', 'Student'
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            // Login Tracking
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
