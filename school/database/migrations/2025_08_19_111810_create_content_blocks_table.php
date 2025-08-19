<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('page_sections')->cascadeOnDelete();
            $table->enum('type', ['text', 'image', 'video', 'list', 'timeline', 'gallery'])->default('text');
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('content')->nullable();
            $table->json('metadata')->nullable(); // For flexible data storage
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};
