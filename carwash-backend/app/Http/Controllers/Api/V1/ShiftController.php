<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ShiftResource; // Buni keyinroq yaratamiz
use App\Models\Shift;
use Illuminate\Http\Request;
/**
 * @OA\Tag(
 *     name="Shifts",
 *     description="Smenalar (ish vaqtlari) boshqaruvi"
 * )
 */
class ShiftController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/shifts/open",
     *     summary="Open a new shift for the authenticated user (Kassirning yangi smenasini ochish)",
     *     tags={"Shifts"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"opening_cash"},
     *             @OA\Property(property="opening_cash", type="integer", description="Smena boshlanishidagi kassa qoldig'i")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Shift opened successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ShiftResource") 
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="User already has an open shift",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You already have an open shift.")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized action")
     * )
     */
    public function store(Request $request)
    {
        $this->authorize('create', Shift::class);
        $user = $request->user();

        // Kassirning allaqachon ochiq smenasi bormi? (closed_at NULL bo'lsa)
        $existingShift = Shift::where('user_id', $user->id)->whereNull('closed_at')->first();
        if ($existingShift) {
            return response()->json(['message' => 'You already have an open shift.'], 409); // 409 Conflict
        }

        $request->validate([
            'opening_cash' => 'required|integer|min:0',
        ]);

        $shift = Shift::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'opened_at' => now(),
            'opening_cash' => $request->opening_cash,
        ]);

        // return new ShiftResource($shift);
        return response()->json($shift, 201);
    }

    /**
     * @OA\Put(
     *     path="/v1/shifts/close",
     *     summary="Close the currently open shift for the authenticated user (Ochiq smenani yopish)",
     *     tags={"Shifts"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"closing_cash"},
     *             @OA\Property(property="closing_cash", type="integer", description="Smena yopilishidagi kassa qoldig'i")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shift closed successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ShiftResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No open shift found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\Models\Shift.")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized action")
     * )
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // Kassirning ochiq smenasini topamiz
        $shift = Shift::where('user_id', $user->id)->whereNull('closed_at')->firstOrFail();

        $this->authorize('update', $shift);

        $request->validate([
            'closing_cash' => 'required|integer|min:0',
        ]);

        $shift->update([
            'closed_at' => now(),
            'closing_cash' => $request->closing_cash,
        ]);

        // return new ShiftResource($shift);
        return response()->json($shift);
    }
}

