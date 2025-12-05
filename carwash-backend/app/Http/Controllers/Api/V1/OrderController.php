<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;          // <<< YANGI (Tranzaksiya uchun)

use App\Http\Requests\V1\UpdateOrderRequest;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Resources\V1\OrderResource; // <<< YANGI IMPORT
use App\Events\OrderUpdated; // <<< 1. BU IMPORTNI QO'SHING
use App\Http\Requests\V1\StoreOrderRequest; // <<< YANGI
use App\Models\Service;                     // <<< YANGI
use Spatie\QueryBuilder\AllowedFilter; // <<< 1. IMPORT QILAMIZ
use Spatie\QueryBuilder\QueryBuilder;   // <<< 1. IMPORT QILAMIZ


/**
 * @OA\Tag(
 *     name="Orders",
 *     description="Buyurtmalar boshqaruvi"
 * )
 */
class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/orders",
     *     summary="Get list of all orders",
     *     tags={"Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="filter[status]", in="query", @OA\Schema(type="string"), description="Filter by status (e.g., new, in_progress)"),
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OrderResource")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Order::class); // Policy'ni chaqiramiz

        // 2. QueryBuilder'ni qo'llaymiz
        $orders = QueryBuilder::for(Order::class)
            // Relyatsiyalarni oldindan yuklab olamiz
            ->with(['vehicle', 'manager', 'cashier'])
            // Status bo'yicha aniq filtr
            ->allowedFilters([
                AllowedFilter::exact('status'),
                'ticket_no'
            ])
            // Qo'shilgan sanasi va umumiy narxi bo'yicha saralash
            ->allowedSorts(['created_at', 'total'])
            // Xavfsizlik qoidamiz
            ->where('tenant_id', $request->user()->tenant_id)
            // Standart saralash
            ->defaultSort('-created_at')
            ->get();
        return OrderResource::collection($orders);
    }

    /**
     * @OA\Get(
     *     path="/v1/orders/{order}",
     *     summary="Get details for a specific order",
     *     tags={"Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="order", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResource")
     *     )
     * )
     */
    public function show(Order $order)
    {
        $order->load(['tenant', 'vehicle', 'manager', 'cashier', 'items.service', 'items.worker']);
        return new OrderResource($order);
    }

    /**
     * @OA\Post(
     *     path="/v1/orders",
     *     summary="Create a new order with items (in a transaction)",
     *     tags={"Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(property="vehicle_id", type="integer", example=1, description="Optional vehicle ID"),
     *             @OA\Property(property="manager_id", type="integer", example=2, description="Optional manager ID"),
     *             @OA\Property(property="note", type="string", example="Mijoz 1 soatda keladi"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 description="List of services for the order (Order Items)",
     *                 @OA\Items(
     *                     required={"service_id", "qty"},
     *                     @OA\Property(property="service_id", type="integer", example=1),
     *                     @OA\Property(property="worker_id", type="integer", example=3),
     *                     @OA\Property(property="qty", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResource")
     *     )
     * )
     */
    public function store(StoreOrderRequest $request)
    {
        // Validatsiyadan o'tgan ma'lumotlarni olamiz
        $validatedData = $request->validated();

        // Tranzaksiyani boshlaymiz
        $order = DB::transaction(function () use ($validatedData) {

            // 1. Asosiy Order'ni yaratamiz (items'siz)
            $order = Order::create([
                'tenant_id'  => $validatedData['tenant_id'],
                'vehicle_id' => $validatedData['vehicle_id'] ?? null,
                'manager_id' => $validatedData['manager_id'] ?? null,
                'note'       => $validatedData['note'] ?? null,
                'ticket_no'  => 'T-' . time(), // Vaqtinchalik unikal bilet raqami
            ]);

            $subtotal = 0;

            // 2. Har bir 'item'ni Orderga bog'lab yaratamiz
            foreach ($validatedData['items'] as $itemData) {
                // Xizmat narxini bazadan olamiz (xavfsizlik uchun)
                $service = Service::find($itemData['service_id']);
                $unitPrice = $service->price;
                $lineTotal = $unitPrice * $itemData['qty'];

                $order->items()->create([
                    'tenant_id' => $validatedData['tenant_id'], // Buni ham yozib qo'yganimiz yaxshi
                    'service_id'=> $itemData['service_id'],
                    'worker_id' => $itemData['worker_id'] ?? null,
                    'qty'       => $itemData['qty'],
                    'unit_price'=> $unitPrice,
                    'line_total'=> $lineTotal,
                ]);

                $subtotal += $lineTotal;
            }

            // 3. Buyurtmaning umumiy narxlarini yangilaymiz
            $order->subtotal = $subtotal;
            $order->total = $subtotal; // Hozircha chegirma yo'q
            $order->save();

            // Tranzaksiya oxirida tayyor orderni qaytaramiz
            return $order;
        });

        // Natijani to'liq ma'lumotlari bilan qaytaramiz
        $order->load(['vehicle', 'manager', 'cashier', 'items.service', 'items.worker']);

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/v1/orders/{order}",
     *     summary="Update an existing order (Hozircha faqat status va asosiy ma'lumotlar)",
     *     tags={"Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="order", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", enum={"in_progress", "done", "paid", "canceled"}, example="done"),
     *             @OA\Property(property="note", type="string", example="Buyurtma yakunlandi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResource")
     *     )
     * )
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        // Avtorizatsiya: Bu sizda allaqachon bor, lekin Policy'ga o'tkazsak bo'ladi
        $this->authorize('update', $order); // <<< Policy'dan foydalanish yaxshiroq

        // Eskirgan if shartini olib tashlaymiz
        // if ($request->user()->tenant_id !== $order->tenant_id) { ... }

        // Ma'lumotlarni yangilaymiz
        $order->update($request->validated());

        // 2. MANA ENG MUHIM QISM:
        // Agar so'rovda 'status' maydoni bo'lsa (ya'ni status o'zgartirilgan bo'lsa),
        // biz butun tizimga "Order Yangilandi" deb e'lon qilamiz.
        if ($request->has('status')) {
            OrderUpdated::dispatch($order);
        }

        // Yangilangan Order'ni to'liq ma'lumotlari bilan qaytaramiz.
        $order->load(['vehicle', 'manager', 'cashier', 'items.service', 'items.worker']);

        return new OrderResource($order);
    }

    /**
     * @OA\Delete(
     *     path="/v1/orders/{order}",
     *     summary="Delete a specific order",
     *     tags={"Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="order", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Order deleted successfully")
     * )
     */
    public function destroy(Request $request, Order $order) // <<< Request'ni qo'shish kerak
    {
        $this->authorize('delete', $order);
        $order->delete();
        return response()->noContent();
    }


}

