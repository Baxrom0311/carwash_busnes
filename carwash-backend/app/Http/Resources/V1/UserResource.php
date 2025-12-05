<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Resursni massivga o'girish.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,

            // BU YANGILIK: Relyatsiyadan ma'lumot qo'shish
            // Biz bu yerda user'ning rolini uning 'profile' relyatsiyasidan olamiz.
            // 'whenLoaded' metodi shuni tekshiradiki, agar 'profile' relyatsiyasi
            // Controllerda .with('profile') yoki .load('profile') orqali
            // oldindan yuklangan bo'lsa, shundagina bu maydonni javobga qo'shadi.
            // Bu N+1 muammosining oldini olishga yordam beradi.
            'role' => $this->whenLoaded('profile', function () {
                return $this->profile->role;
            }),

            // Biz bu yerda butun tenant obyektini emas, faqat uning nomini qaytaramiz.
            // Bu javobni ixchamlashtiradi.
            'tenantName' => $this->whenLoaded('tenant', function () {
                return $this->tenant->name;
            }),
        ];
    }
}
