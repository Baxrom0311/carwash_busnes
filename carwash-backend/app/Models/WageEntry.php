<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WageEntry extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'worker_id',
        'order_item_id',
        'amount',
        'period_date',
    ];

    protected $casts = [
        'period_date' => 'date',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
