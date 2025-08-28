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
}
