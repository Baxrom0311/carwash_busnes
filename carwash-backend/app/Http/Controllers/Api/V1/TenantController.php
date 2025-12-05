<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Resources\V1\TenantResource;

/**
 * @OA\Tag(
 *     name="Tenants",
 *     description="Ko'p mijozli (Multi-tenancy) boshqaruvi"
 * )
 */
class TenantController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/tenants",
     *     summary="Get list of all tenants (usually for Owner/Admin)",
     *     tags={"Tenants"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of tenants",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TenantResource")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Tenant::class); // Authorizatsiya ishlatiladi
        $tenants = Tenant::all();
        return TenantResource::collection($tenants);
    }

    /**
     * @OA\Get(
     *     path="/v1/tenants/{tenant}",
     *     summary="Get details for a specific tenant",
     *     tags={"Tenants"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="tenant", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Tenant details",
     *         @OA\JsonContent(ref="#/components/schemas/TenantResource")
     *     )
     * )
     */
    public function show(Tenant $tenant)
    {
        $this->authorize('view', $tenant);
        return new TenantResource($tenant);
    }
    // ... store, update, destroy metodlari ham shunga o'xshash tuziladi ...
}

