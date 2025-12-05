<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreVehicleRequest;
use App\Http\Requests\V1\UpdateVehicleRequest;
use App\Http\Resources\V1\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/vehicles",
     *     summary="Get list of all vehicles",
     *     tags={"Vehicles"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of vehicles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/VehicleResource")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Vehicle::class);

        $vehicles = Vehicle::where('tenant_id', $request->user()->tenant_id)->get();

        return VehicleResource::collection($vehicles);
    }

    /**
     * @OA\Post(
     *     path="/v1/vehicles",
     *     summary="Create a new vehicle",
     *     tags={"Vehicles"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plate_number"},
     *             @OA\Property(property="plate_number", type="string", example="70 A 777 AA"),
     *             @OA\Property(property="brand", type="string", example="Chevrolet")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Vehicle created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/VehicleResource")
     *     )
     * )
     */
    public function store(StoreVehicleRequest $request)
    {
        $this->authorize('create', Vehicle::class);
        $validated = $request->validated();
        $validated['tenant_id'] = $request->user()->tenant_id;
        $vehicle = Vehicle::create($validated);

        return (new VehicleResource($vehicle))->response()->setStatusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/v1/vehicles/{vehicle}",
     *     summary="Update an existing vehicle",
     *     tags={"Vehicles"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="vehicle", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="plate_number", type="string", example="70 A 777 AB"),
     *             @OA\Property(property="color", type="string", example="Yashil")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/VehicleResource")
     *     )
     * )
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);
        $vehicle->update($request->validated());

        return new VehicleResource($vehicle);
    }

    /**
     * @OA\Delete(
     *     path="/v1/vehicles/{vehicle}",
     *     summary="Delete a specific vehicle",
     *     tags={"Vehicles"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="vehicle", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Vehicle deleted successfully")
     * )
     */
    public function destroy(Vehicle $vehicle)
    {
        $this->authorize('delete', $vehicle);
        $vehicle->delete();

        return response()->noContent();
    }
}

