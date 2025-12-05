<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <<< IMPORT QILAMIZ

class TenantInvoice extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'tenant_id',
        'period_month',
        'amount',
        'status',
        'due_at',
        'paid_at',
        'payment_ref',
    ];

    protected $casts = [
        'period_month' => 'date',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
    ];
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
    public function paymeTransaction(): HasMany
    {
        return $this->hasMany(PaymeTransaction::class, 'tenant_invoice_id');
    }
}
