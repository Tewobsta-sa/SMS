<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'title',
        'po_box',
        'address',
        'opening_hours',
        'map_url',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'opening_hours' => 'array', 
    ];

    
    public static function getDayOptions(): array
    {
        return [
            'mon' => 'Monday',
            'tue' => 'Tuesday',
            'wed' => 'Wednesday',
            'thu' => 'Thursday',
            'fri' => 'Friday',
            'sat' => 'Saturday',
            'sun' => 'Sunday',
        ];
    }
}
