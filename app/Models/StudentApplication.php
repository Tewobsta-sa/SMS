<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'school_id',
        'parent_id',
        'document_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'address',
        'parent_name',
        'parent_relation',
        'parent_phone',
        'parent_email',
        'occupation',
        'status',
        'remarks',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
    ];

    /**
     * Get the parent that owns the student application.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Get the school that the student is applying to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
    
    /**
     * Get the document associated with the application.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
