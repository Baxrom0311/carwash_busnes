<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use App\Http\Resources\V1\PaymentResource; // Resource mavjud deb faraz qilamiz

class PaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/payments",
     *     summary="Get list of all payments",
     *     tags={"Payments"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="List of payments"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        // Xavfsizlik: faqat o'z tenant'iga tegishli to'lovlarni ko'rsatish
        $this->authorize('viewAny', Payment::class);

        $payments = QueryBuilder::for(Payment::class)
            ->with('order') // Buyurtma ma'lumotlarini yuklash
            ->allowedFilters([
                'method',
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts([
                AllowedSort::field('paid_at'),
                AllowedSort::field('amount')
            ])
            ->where('tenant_id', $request->user()->tenant_id)
            ->defaultSort('-paid_at')
            ->paginate(10); // Sahifalash (Pagination)

        return PaymentResource::collection($payments); // Resource ishlatish yaxshiroq
    }

    /**
     * @OA\Get(
     *     path="/v1/payments/{payment}",
     *     summary="Get details for a specific payment",
     *     tags={"Payments"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Payment details"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function show(Request $request, Payment $payment)
    {
        $this->authorize('view', $payment);
        return new PaymentResource($payment->load('order'));
    }
}