<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Car Wash Admin API Documentation",
 *     description="API documentation for Car Wash Admin backend application (Laravel/Sanctum)",
 *     @OA\License(name="MIT")
 * )
 * @OA\Server(
 *     url="http://127.0.0.1:8000/api",
 *     description="Local Development Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum",
 *     description="Enter Sanctum token for authentication (Bearer Token)"
 * )
 *
 * @OA\Schema(
 *     schema="UserResource",
 *     title="User Resource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Alijon Valiyev"),
 *     @OA\Property(property="phone", type="string", example="998901234567"),
 *     @OA\Property(property="email", type="string", example="ali@example.com"),
 *     @OA\Property(property="tenantId", type="integer", example=1),
 *     @OA\Property(property="role", type="string", example="manager"),
 *     @OA\Property(property="createdAt", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="StoreUserRequest",
 *     title="Store User Request",
 *     type="object",
 *     required={"name", "phone", "role"},
 *     @OA\Property(property="name", type="string", example="Yangi Xodim"),
 *     @OA\Property(property="phone", type="string", example="998901112233"),
 *     @OA\Property(property="email", type="string", example="yangi@example.com"),
 *     @OA\Property(property="password", type="string", example="password123"),
 *     @OA\Property(property="role", type="string", enum={"owner", "manager", "cashier", "worker"}, example="worker")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateUserRequest",
 *     title="Update User Request",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="Yangi Ism"),
 *     @OA\Property(property="phone", type="string", example="998901112233"),
 *     @OA\Property(property="password", type="string", example="new_password")
 * )
 *
 * @OA\Schema(
 *     schema="ServiceResource",
 *     title="Service Resource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Standart yuvish"),
 *     @OA\Property(property="price", type="number", format="float", example="25000.00"),
 *     @OA\Property(property="description", type="string", example="Avtomobil tashqi qismini tozalash"),
 *     @OA\Property(property="isActive", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="VehicleResource",
 *     title="Vehicle Resource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="plateNumber", type="string", example="70 A 777 AA"),
 *     @OA\Property(property="brand", type="string", example="Chevrolet"),
 *     @OA\Property(property="model", type="string", example="Malibu 2"),
 *     @OA\Property(property="color", type="string", example="Qora"),
 *     @OA\Property(property="ownerName", type="string", example="Bobur Akramov"),
 *     @OA\Property(property="ownerPhone", type="string", example="998905554433")
 * )
 *
 * @OA\Schema(
 *     schema="OrderResource",
 *     title="Order Resource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="ticketNo", type="string", example="T-1701234567"),
 *     @OA\Property(property="status", type="string", enum={"new", "in_progress", "done", "paid", "canceled"}, example="new"),
 *     @OA\Property(property="total", type="number", format="float", example="50000.00"),
 *     @OA\Property(property="manager", ref="#/components/schemas/UserResource")
 * )
 *
 * @OA\Schema(
 *     schema="ShiftResource",
 *     title="Shift Resource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="userId", type="integer", example=2),
 *     @OA\Property(property="status", type="string", enum={"open", "closed"}, example="open"),
 *     @OA\Property(property="openTime", type="string", format="date-time"),
 *     @OA\Property(property="closeTime", type="string", format="date-time", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="TenantResource",
 *     title="Tenant Resource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Farg'ona Carwash"),
 *     @OA\Property(property="phone", type="string", example="998901234567")
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

