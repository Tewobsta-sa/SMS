<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'school_id', 'payment_id', 'receipt_number', 
        'issued_date', 'issued_by'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
