<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->foreignId('assignment_id')->nullable()->constrained('assignments')->cascadeOnDelete()->after('exam_id');
            $table->decimal('weight', 5, 2)->nullable()->after('percentage');
            $table->boolean('is_final')->default(false)->after('weight');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->dropColumn(['assignment_id', 'weight', 'is_final']);
        });
    }
};
