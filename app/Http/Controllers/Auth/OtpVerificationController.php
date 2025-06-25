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