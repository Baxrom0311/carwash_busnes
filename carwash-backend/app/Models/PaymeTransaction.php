<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymeTransaction extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'tenant_id',
        'tenant_invoice_id',
        'paycom_id',
        'paycom_time',
        'state',
        'amount',
        'currency',
        'perform_time',
        'cancel_time',
        'cancel_reason',
    ];
    protected $casts = [
        'perform_time' => 'timestamp',
        'cancel_time' => 'timestamp',
        'paycom_time' => 'timestamp',
    ];
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(TenantInvoice::class, 'tenant_invoice_id');
    }
}
