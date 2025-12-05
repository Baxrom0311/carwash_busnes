<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable; // <<< IMPORT QILAMIZ

class Tenant extends Model
{
    //
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'slug',
        'phone',
        'address',
        'subscription_status',
        'monthly_fee',
        'next_billing_date',
    ];
    protected $casts = [
        'next_billing_date' => 'date',
    ];
    public function users():HasMany{return $this->hasMany(User::class);}
    public function orders(): HasMany{return $this->hasMany (Order::class);}
    public function vehicles(): hasMany{ return $this->hasMany (Vehicle::class);}
    public function payments(): HasMany {return $this->hasMany (Payment::class);}
    public function wageRules(): HasMany {return $this->hasMany (WageRule::class);}
    public function shifts(): HasMany {return $this->hasMany (Shift::class);}
    public function invoices(): HasMany {return $this->hasMany(TenantInvoice::class);}

    public function services(): HasMany{return $this->hasMany (Service::class);}
    public function routeNotificationForTelegram()
    {
        // Tenantning 'owner' rolidagi user'ini topamiz (yoki birinchi user'ni)
        $owner = $this->users()->first(); // Sodda usul
        return $owner?->telegram_chat_id;
    }
}
