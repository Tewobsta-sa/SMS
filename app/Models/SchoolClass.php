<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\SoftDeletes;
class SchoolClass extends Model {
    use HasUserStamps,SoftDeletes;
    protected $fillable = [
        'school_id',
        'name',
        'code',
        'academic_year',
    ];

    public function school() {
        return $this->belongsTo( School::class );
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
