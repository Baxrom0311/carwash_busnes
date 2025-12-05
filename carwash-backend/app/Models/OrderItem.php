<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    //

    use HasFactory;
    protected $fillable = [
        'tenant_id',
        'order_id',
        'service_id',
        'worker_id',
        'qty',
        'unit_price',
        'line_total',
    ];
    public function order(): BelongsTo
    {
        return $this->belongsTo (Order::class);
    }
    public function service(): BelongsTo
    {
        return $this->belongsTo (Service::class);
    }
    public function worker(): BelongsTo
    {
        return $this->belongsTo (User::class);
    }
}
