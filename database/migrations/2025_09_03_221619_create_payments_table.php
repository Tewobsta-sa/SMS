
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('fee_id')->constrained('fee_structures')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');

            $table->decimal('expected_amount', 10, 2); // from fee structure
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            
            $table->date('payment_date')->nullable();
            $table->enum('payment_method', ['Online', 'Cash', 'Bank'])->default('Cash');
            $table->string('tx_ref')->nullable()->unique(); // unique transaction reference
            $table->string('gateway')->nullable();
            $table->enum('status', ['Paid','Pending','Failed'])->default('Pending');
            
            $table->string('receipt_no')->nullable();
            $table->timestamp('confirmed_at')->nullable(); // webhook confirmation
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};