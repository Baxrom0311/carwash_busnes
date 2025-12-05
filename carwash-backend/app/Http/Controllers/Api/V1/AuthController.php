<?php

namespace App\Http\Controllers\Api\V1;

use App\Notifications\SendOtpNotification;
use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication endpoints"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/login",
     *     summary="User login (phone/password) - Hozircha faqat telefon raqami tekshiriladi",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"phone"},
     *             @OA\Property(property="phone", type="string", example="998901234567"),
     *             @OA\Property(property="password", type="string", example="secret_password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Muvaffaqiyatli kirish",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource"),
     *             @OA\Property(property="access_token", type="string", example="1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validatsiya xatosi"
     *     )
     * )
     */
    public function login(Request $request)
    {
        // 1. Kelgan ma'lumotni tekshiramiz (validatsiya)
        $request->validate([
            'phone' => 'required|string',
            // Hozircha parolni majburiy qilmaymiz
            // 'password' => 'required|string',
        ]);

        // 2. Telefon raqami bo'yicha foydalanuvchini qidiramiz
        $user = User::where('phone', $request->phone)->first();

        // 3. Agar user topilmasa yoki paroli to'g'ri kelmasa...
        // Hozircha parolni tekshirmaymiz: if (! $user || ! Hash::check($request->password, $user->password)) {
        if (! $user) {
            // ...xatolik qaytaramiz.
            throw ValidationException::withMessages([
                'phone' => ['Bunday telefon raqami ro\'yxatdan o\'tmagan.'],
            ]);
        }

        // 4. Agar hammasi to'g'ri bo'lsa, user uchun yangi token yaratamiz
        // "auth_token" - bu tokenga biz berayotgan nom.
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Foydalanuvchi ma'lumotlari va token'ni javob sifatida qaytaramiz
        return response()->json([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/v1/otp/send",
     *     summary="Send OTP code to user via Telegram",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"phone"},
     *             @OA\Property(property="phone", type="string", example="998901234567")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP code successfully sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Verification code has been sent via Telegram.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Foydalanuvchi topilmadi"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validatsiya xatosi"
     *     )
     * )
     */
    public function sendOtp(Request $request)
    {
        // 1. Validatsiya
        $request->validate(['phone' => 'required|string']);
        $phone = $request->phone;

        // 2. Foydalanuvchi mavjudligini tekshiramiz
        $user = User::where('phone', $phone)->firstOrFail(); // topilmasa 404 qaytaradi

        // ---- TEST UCHUN VAQTINCHALIK QISM ----
        // Har safar o'zimizning Chat ID'mizni user'ga yozib qo'yamiz.
        // Haqiqiy loyihada bu qism bo'lmaydi, chat_id registratsiyada saqlanadi.
        $user->update(['telegram_chat_id' => '5146421024']); // SIZNING CHAT ID'NGIZ
        // ------------------------------------

        // 3. Yangi OTP kod generatsiya qilamiz
        $code = rand(100000, 999999); // 6 xonali qilganimiz yaxshiroq

        // 4. Eski, ishlatilmagan kodlarni bekor qilamiz (ixtiyoriy, lekin yaxshi amaliyot)
        OtpCode::where('phone', $phone)->where('used', false)->update(['used' => true]);

        // 5. Yangi kodni bazaga saqlaymiz.
        // XAVFSIZLIK: Kodni xeshlangan holda saqlaymiz.
        OtpCode::create([
            'tenant_id'  => $user->tenant_id,
            'phone'      => $phone,
            'code_hash'  => Hash::make($code), // O'ZGARTIRILDI: Kodni xeshlash
            'expires_at' => now()->addMinutes(3),
        ]);

        // 6. Notification (Telegram xabari) yuboramiz
        $user->notify(new SendOtpNotification($code));

        // 7. Muvaffaqiyatli javob qaytaramiz
        return response()->json(['message' => 'Verification code has been sent via Telegram.']);
    }

    /**
     * @OA\Post(
     *     path="/v1/otp/verify",
     *     summary="Verify OTP code and issue a Sanctum token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"phone", "code"},
     *             @OA\Property(property="phone", type="string", example="998901234567"),
     *             @OA\Property(property="code", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful authentication",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource"),
     *             @OA\Property(property="access_token", type="string", example="1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Noto'g'ri kod yoki muddati o'tgan"
     *     )
     * )
     */
    public function verifyOtp(Request $request)
    {
        // 1. Validatsiya
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string',
        ]);

        $phone = $request->phone;
        $code = $request->code;

        // 2. Tegishli OTP yozuvini bazadan qidiramiz
        // XAVFSIZLIK: Endi faqat muddatini va ishlatilmaganligini tekshiramiz.
        $otp = OtpCode::where('phone', $phone)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest() // Eng oxirgi yuborilgan kodni olamiz
            ->first();

        // 3. Agar kod topilmasa yoki noto'g'ri bo'lsa
        if (!$otp || !Hash::check($code, $otp->code_hash)) { // Hash::check bilan tekshirish
            throw ValidationException::withMessages([
                'code' => ['The provided code is invalid or has expired.'],
            ]);
        }

        // 4. Foydalanuvchini topamiz
        $user = User::where('phone', $phone)->firstOrFail();

        // 5. OTP'ni "ishlatildi" deb belgilaymiz
        $otp->update(['used' => true]);

        // 6. Foydalanuvchi uchun yangi token yaratamiz
        $token = $user->createToken('auth_token')->plainTextToken;

        // 7. Foydalanuvchi ma'lumotlari va token'ni qaytaramiz
        return response()->json([
            'user'         => $user, // Kelajakda UserResource'dan foydalanamiz
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/v1/logout",
     *     summary="Logout the authenticated user",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        // Tizimga kirgan foydalanuvchining AYNAN HOZIR ishlatayotgan tokenini
        // bazadagi 'personal_access_tokens' jadvalidan o'chiradi.
        $request->user()->currentAccessToken()->delete();

        // Muvaffaqiyatli javob qaytaramiz.
        return response()->json(['message' => 'Successfully logged out.'], 200);
    }
}

