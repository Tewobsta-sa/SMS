<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'school_id',
        'user_id',
        'employee_no',
        'hire_date',
        'department',
        'specialization',
        'qualification',
        'qualifications',
        'workload',
        'experience_years',
    ];
    public function school() {
        return $this->belongsTo(School::class);
    }

    public function user(){ return $this->belongsTo(User::class); }


}
