<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'school_id',
        'user_id',
        'registration_no',
        'admission_no',
        'admission_number',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'health_info',
        'family_info',
        'transfer_history',
        'immunization',
        'immunization_record',
        'guardian_id',
        'class_id',
        'section_id',
        'status',
        'registration_status',
        'enrollment_date',
    ];

    public function school() {
        return $this->belongsTo(School::class);
    }

    public function user(){ return $this->belongsTo(User::class); }
    public function guardian(){ return $this->belongsTo(\App\Models\ParentModel::class, 'guardian_id'); }
    public function classModel(){ return $this->belongsTo(\App\Models\SchoolClass::class, 'class_id'); }
    public function section(){ return $this->belongsTo(\App\Models\Section::class, 'section_id'); }
}
