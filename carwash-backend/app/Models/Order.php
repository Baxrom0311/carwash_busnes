<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// << Yaxshilanish 1
class Order extends Model
{
    //
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'tenant_id',
        'vehicle_id',
        'ticket_no',
        'status',
        'checkin_at',
        'checkout_at',
        'manager_id',
        'cashier_id',
        'subtotal',
        'discount',
        'total',
        'note',
    ];
    protected $casts = [
        'checkin_at' => 'datetime',
        'checkout_at' => 'datetime',
    ];
    public function tenant(): BelongsTo
    {
        return $this->belongsTo (Tenant::class);
    }
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo (Vehicle::class);
    }
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function items(): HasMany
    {
        return $this->hasMany (OrderItem::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany (Payment::class);
    }
}
