<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'tenant_id',
        'order_id',
        'amount',
        'method',
        'paid_at',
        'ref',
    ];
    protected $casts = [
        'paid_at' => 'datetime',
    ];
    public function order(): BelongsTo
    {
        return $this->belongsTo (Order::class);
    }
}
