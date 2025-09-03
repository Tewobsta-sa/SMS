<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'school_id', 'student_id', 'invoice_number', 'fee_id',
        'total_amount', 'balance_remaining', 
        'due_date', 'issued_date', 'status'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_id');
    }
}