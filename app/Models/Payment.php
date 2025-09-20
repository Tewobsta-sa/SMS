<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'tx_ref',
        'name',
        'email',
        'amount',
        'status',
        'raw_init',
        'raw_verify',
    ];

    // If you're using JSON columns for raw_init/raw_verify, cast them:
    protected $casts = [
        'raw_init'   => 'array',
        'raw_verify' => 'array',
    ];
}
