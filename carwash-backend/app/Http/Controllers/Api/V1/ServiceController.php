<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreServiceRequest;
use App\Http\Requests\V1\UpdateServiceRequest;
use App\Http\Resources\V1\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;

use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class ServiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/services",
     *     summary="Get list of all services",
     *     tags={"Services"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="filter[is_active]", in="query", @OA\Schema(type="boolean"), description="Filter by active status"),
     *     @OA\Response(
     *         response=200,
     *         description="List of services",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ServiceResource")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Service::class);

        $services = QueryBuilder::for(Service::class)
            ->allowedFilters(['name', AllowedFilter::exact('is_active')])
            ->where('tenant_id', $request->user()->tenant_id)
            ->get();

        return ServiceResource::collection($services);
    }

    /**
     * @OA\Post(
     *     path="/v1/services",
     *     summary="Create a new service",
     *     tags={"Services"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price"},
     *             @OA\Property(property="name", type="string", example="Yangi yuvish"),
     *             @OA\Property(property="price", type="number", format="float", example="35000.00"),
     *             @OA\Property(property="description", type="string", example="Yangi xizmat tavsifi"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ServiceResource")
     *     )
     * )
     */
    public function store(StoreServiceRequest $request)
    {
        $this->authorize('create', Service::class);
        $validated = $request->validated();
        $validated['tenant_id'] = $request->user()->tenant_id;
        $service = Service::create($validated);

        return (new ServiceResource($service))->response()->setStatusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/v1/services/{service}",
     *     summary="Update an existing service",
     *     tags={"Services"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="service", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Yangilangan yuvish"),
     *             @OA\Property(property="price", type="number", format="float", example="40000.00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ServiceResource")
     *     )
     * )
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $this->authorize('update', $service);
        $service->update($request->validated());

        return new ServiceResource($service);
    }

    /**
     * @OA\Delete(
     *     path="/v1/services/{service}",
     *     summary="Delete a specific service",
     *     tags={"Services"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="service", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Service deleted successfully")
     * )
     */
    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        $service->delete();

        return response()->noContent();
    }
}
