<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'school_id', 'grade_id', 'section_id', 
        'category_id', 'description', 'fee_type', 
        'amount', 'due_date', 'is_recurring'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function Grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'fee_id');
    }
}
