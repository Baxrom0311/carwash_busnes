<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'phone',
        'code_hash',
        'expires_at',
        'attempts',
        'used',
    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

}
