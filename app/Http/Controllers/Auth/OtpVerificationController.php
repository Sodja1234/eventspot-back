<?php

// app/Http/Controllers/Auth/OtpVerificationController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OtpVerificationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/verify",
     *     summary="Verify email address using OTP",
     *     description="Verify a user's email address by submitting a valid OTP code.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully via OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email address successfully verified via OTP."),
     *             @OA\Property(property="user", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid or expired OTP code",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid or expired OTP code.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     )
     * )
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();
        $otpRecord = EmailOtp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'Code OTP invalide ou expiré.',
            ], 422);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // Supprimer l'OTP après vérification
        $otpRecord->delete();

        return response()->json([
            'message' => 'Adresse e-mail vérifiée avec succès via OTP.',
            'user' => $user
        ]);
    }
}