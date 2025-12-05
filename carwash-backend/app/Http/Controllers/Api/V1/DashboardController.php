<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // Sana bilan ishlash uchun
use Illuminate\Support\Facades\DB;   // To'g'ridan-to'g'ri SQL so'rovlari uchun
// ... kerakli importlar ...

/**
 * @OA\Get(
 *     path="/v1/dashboard",
 *     summary="Get all dashboard statistics",
 *     tags={"Dashboard"},
 *     security={{"sanctum": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Dashboard ma'lumotlari",
 *         @OA\JsonContent(
 *             @OA\Property(property="todaysRevenue", type="number", example="150000.00"),
 *             @OA\Property(property="todaysOrdersCount", type="integer", example="5"),
 *             @OA\Property(property="inProgressOrdersCount", type="integer", example="2"),
 *             @OA\Property(property="topWorker", type="object", example={"name": "Alisher Olimov"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */
class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Tizimga kirgan foydalanuvchi va uning tenant'ini olamiz
        $user = $request->user();
        $tenantId = $user->tenant_id;
        $today = Carbon::today();

        // 1. Bugungi tushum (faqat 'paid' statusidagi orderlar)
        $todaysRevenue = Order::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->whereDate('updated_at', $today) // updated_at sanasi bugunga teng bo'lganlar
            ->sum('total');

        // 2. Bugungi buyurtmalar soni
        $todaysOrdersCount = Order::where('tenant_id', $tenantId)
            ->whereDate('created_at', $today)
            ->count();

        // 3. Hozirda bajarilayotgan buyurtmalar soni
        $inProgressOrdersCount = Order::where('tenant_id', $tenantId)
            ->where('status', 'in_progress')
            ->count();

        // 4. Eng ko'p ish bajargan ishchi (shu oyda)
        $topWorker = DB::table('order_items')
            ->join('users', 'order_items.worker_id', '=', 'users.id')
            ->where('order_items.tenant_id', $tenantId)
            ->whereMonth('order_items.created_at', now()->month)
            ->select('users.name', DB::raw('count(order_items.id) as items_count'))
            ->groupBy('users.name')
            ->orderByDesc('items_count')
            ->first();

        // Barcha ma'lumotlarni yig'ib, JSON formatida qaytaramiz
        return response()->json([
            'todaysRevenue' => (int) $todaysRevenue,
            'todaysOrdersCount' => $todaysOrdersCount,
            'inProgressOrdersCount' => $inProgressOrdersCount,
            'topWorker' => $topWorker,
        ]);
    }
}

