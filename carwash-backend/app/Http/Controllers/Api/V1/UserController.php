<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreUserRequest;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Actions\Users\CreateNewUserAction; // <<< YANGI IMPORT

/**
 * @OA\Tag(
 *     name="Users",
 *     description="Xodimlar (User) boshqaruvi"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/users",
     *     summary="Get list of all staff users",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action (Policy)"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('profile')
            ->where('tenant_id', $request->user()->tenant_id)
            ->get();

        return UserResource::collection($users);
    }

    /**
     * @OA\Post(
     *     path="/v1/users",
     *     summary="Create a new staff user",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreUserRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StoreUserRequest $request, CreateNewUserAction $createNewUserAction)
    {
        $this->authorize('create', User::class);

        // O'ZGARISH: Barcha murakkab logika o'rniga, shunchaki Action'ni chaqiramiz.
        $user = $createNewUserAction->handle(
            $request->validated(),
            $request->user()->tenant_id
        );

        return (new UserResource($user->load('profile')))->response()->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/v1/users/{user}",
     *     summary="Get details for a specific user",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        return new UserResource($user->load('profile', 'tenant'));
    }

    /**
     * @OA\Put(
     *     path="/v1/users/{user}",
     *     summary="Update an existing user",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUserRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $user) {
            $user->update([
                'name' => $validated['name'] ?? $user->name,
                'phone' => $validated['phone'] ?? $user->phone,
                'email' => $validated['email'] ?? $user->email,
                'password' => isset($validated['password']) ? Hash::make($validated['password']) : $user->password,
            ]);

            if (isset($validated['role'])) {
                $user->profile()->update([
                    'role' => $validated['role'],
                ]);
            }
        });

        return new UserResource($user->load('profile'));
    }

    /**
     * @OA\Delete(
     *     path="/v1/users/{user}",
     *     summary="Delete a specific user",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="You cannot delete yourself or unauthorized"
     *     )
     * )
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // O'z-o'zini o'chirishga yo'l qo'ymaymiz (ixtiyoriy)
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot delete yourself.'], 403);
        }

        $user->delete();

        return response()->noContent();
    }
}

