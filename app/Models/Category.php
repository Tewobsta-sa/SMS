<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    //
    use HasFactory;

    protected $fillable = ['school_id', 'name', 'description', 'code'];

    protected static function booted(): void
    {
        // Apply the multi-tenancy scope to all queries.
        static::addGlobalScope(new SchoolScope);

        /**
         * This 'creating' event will now handle EVERYTHING automatically
         * before a new category is saved to the database.
         */
        static::creating(function ($category) {
            // 1. Ensure school_id is set securely, if not already present.
            if (Auth::check() && is_null($category->school_id)) {
                $category->school_id = Auth::user()->school_id;
            }

            // 2. Generate the unique code within a transaction to prevent race conditions.
            // Note: This logic now lives entirely within the model's lifecycle.
            if (is_null($category->code)) {
                $category->code = DB::transaction(function () use ($category) {
                    $lastCategory = self::where('school_id', $category->school_id)
                                        ->lockForUpdate()
                                        ->latest('id')
                                        ->first();

                    $nextNumber = $lastCategory ? $lastCategory->id + 1 : 1;
                    
                    $prefix = 'CAT';
                    $numberPart = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                    $namePart = substr(preg_replace('/[^A-Za-z0-9]/', '', strtoupper($category->name)), 0, 4);

                    return "{$prefix}-{$numberPart}-{$namePart}";
                });
            }
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }
}
