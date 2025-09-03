<?php

namespace App\Console\Commands;

use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateRecurringInvoices extends Command
{
    protected $signature = 'invoices:generate-recurring';
    protected $description = 'Generate recurring monthly invoices for all applicable students';

    public function handle()
    {
        $this->info('Starting recurring invoice generation...');
        Log::info('Starting recurring invoice generation job.');

        // 1. Get all recurring fees, grouped by their applicability.
        $recurringFees = FeeStructure::where('is_recurring', true)->get();
        if ($recurringFees->isEmpty()) {
            $this->info('No recurring fee structures found. Exiting.');
            return 0;
        }

        // 2. Find all students who need invoices, chunking for performance.
        Student::with(['school', 'class', 'section'])
            ->chunk(200, function ($students) use ($recurringFees) {
                
                $invoicesToCreate = [];

                foreach ($students as $student) {
                    // 3. Find fees that DIRECTLY apply to this student
                    $applicableFees = $recurringFees->where('school_id', $student->school_id)
                        ->where('class_id', $student->class_id)
                        ->where('section_id', $student->section_id);

                    foreach ($applicableFees as $fee) {
                        // 4. Check for an existing invoice for this fee for THIS month and year
                        $invoiceExists = Invoice::where('student_id', $student->id)
                            ->where('fee_id', $fee->id)
                            ->whereYear('issued_date', now()->year)
                            ->whereMonth('issued_date', now()->month)
                            ->exists();

                        if ($invoiceExists) {
                            continue; // Skip if invoice already exists for this month
                        }

                        // Due date calculation logic
                        $originalDay = Carbon::parse($fee->due_date)->day;
                        $currentMonthDue = Carbon::now()->setDay($originalDay)->startOfDay();
                        if ($currentMonthDue->day !== $originalDay) {
                           $currentMonthDue->endOfMonth();
                        }
                        
                        // 5. Prepare the invoice data for batch insertion
                        $invoicesToCreate[] = [
                            'school_id' => $student->school_id,
                            'student_id' => $student->id,
                            'fee_id' => $fee->id,
                            'invoice_number' => 'INV-' . strtoupper(uniqid()), // Consider a better generator
                            'total_amount' => $fee->amount,
                            'balance_remaining' => $fee->amount,
                            'due_date' => $currentMonthDue,
                            'issued_date' => now(),
                            'status' => 'Unpaid',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                // 6. Insert all new invoices for this chunk in ONE query.
                if (!empty($invoicesToCreate)) {
                    Invoice::insert($invoicesToCreate);
                    $this->info(count($invoicesToCreate) . ' invoices created for this chunk.');
                    Log::info(count($invoicesToCreate) . ' invoices created for a chunk.');
                }
            });

        $this->info('Recurring invoice generation completed successfully.');
        Log::info('Recurring invoice generation job finished.');
        return 0;
    }
}