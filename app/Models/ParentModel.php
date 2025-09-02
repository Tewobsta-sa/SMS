<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    protected $fillable = [
        'school_id',
        'user_id',
        'occupation',
        'relation',
    ];

    protected $table = 'parent_models';

    public function getUserNameAttribute(): ?string
    {
        return $this->user?->name;
    }

    public function user(){ return $this->belongsTo(User::class); }

    public function school() {
        return $this->belongsTo(School::class);
    }

    public function students() {
        return $this->hasMany(Student::class, 'guardian_id');
    }
}
