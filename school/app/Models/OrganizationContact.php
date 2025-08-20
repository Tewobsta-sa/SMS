<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class OrganizationContact extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'type',
        'value',
        'status'
    ];

    protected $casts = [
        'type' => 'string',
        'status'=> StatusEnum::class,

    ];

  
    public static function getTypeOptions(): array
    {
        return [
            'phone' => 'Phone',
            'fax' => 'Fax',
            'email' => 'Email',
        ];
    }
}
