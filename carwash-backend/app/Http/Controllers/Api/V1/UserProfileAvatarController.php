<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\V1\UserResource;

/**
 * @OA\Tag(
 *     name="Profile",
 *     description="Foydalanuvchi profilini boshqarish"
 * )
 */
class UserProfileAvatarController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/users/{user}/avatar",
     *     summary="Upload a new avatar for the user",
     *     tags={"Profile"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer"), description="User ID"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"avatar"},
     *                 @OA\Property(property="avatar", type="string", format="binary", description="Image file for the avatar")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Avatar uploaded successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(response=422, description="Invalid file format or size")
     * )
     */
    public function __invoke(Request $request, User $user)
    {
        // 1. Avtorizatsiya: Bu userni o'zgartirishga haqqim bormi? (Original security check retained)
        $this->authorize('update', $user);

        // 2. Validatsiya: Kelgan fayl rasmmi? Hajmi qancha?
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // 3. Yangi faylni 'public' diskidagi 'avatars/{tenant_id}/{user_id}' papkasiga saqlaymiz
        // storePublicly funksiyasi faylni saqlab, yo'lni qaytaradi.
        $path = $request->file('avatar')->storePublicly("avatars/{$user->tenant_id}/{$user->id}", 'public');
        // 4. Agar user'ning eski rasmi bo'lsa, uni o'chirib tashlaymiz
        if ($user->profile->avatar_path) {
            Storage::disk('public')->delete($user->profile->avatar_path);
        }

        // 5. Saqlangan yo'lni bazaga yozamiz
        $user->profile->update(['avatar_path' => $path]);

        // 6. Javobni UserResource formatida qaytaramiz
        return new UserResource($user->load('profile'));
    }
}

