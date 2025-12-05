<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffProfile extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'avatar_path', // <<< QO'SHING
        'passport_series',
        'passport_number',
        'pinfl',
        'address',
        'dob',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'dob' => 'date',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
