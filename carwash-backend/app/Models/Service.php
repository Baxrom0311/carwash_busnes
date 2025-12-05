<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    //

    use HasFactory;
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'price',
        'is_active',
        'description',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
