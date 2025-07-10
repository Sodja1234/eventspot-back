<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class VerifyEmailController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/verify-email/{id}/{hash}",
     *     summary="Verify user email address",
     *     description="Verify the user's email using the user ID and hash. Redirects to frontend login page with verification status.",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to verify",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         description="SHA1 hash of the user's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to frontend login with verification status",
     *         @OA\Header(
     *             header="Location",
     *             description="URL to redirect the user",
     *             @OA\Schema(type="string", example="https://frontend.example.com/login?verified=1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Invalid verification link",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="invalid-link")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
{
    Log::info("Vérification email commencée pour l'utilisateur ID: " . $id);

    $user = User::findOrFail($id); // ✅ CORRECTION ICI

    // Ensure $user is a single User instance, not a collection
    if ($user instanceof \Illuminate\Database\Eloquent\Collection) {
        $user = $user->first();
    }

    Log::info("Utilisateur récupéré: ", ['id' => $user->id, 'email' => $user->email]);

    if (!hash_equals((string) $hash, sha1($user->email))) {
        Log::warning("Hash incorrect pour l'utilisateur ID: " . $user->id);
       // return response()->json(['status' => 'invalid-link'], 403);
        return redirect()->away(config('app.frontend_url').'/login?verified=0');
    }

    if ($user->hasVerifiedEmail()) {
        Log::info("Email déjà vérifié pour l'utilisateur ID: " . $user->id);
       // return response()->json(['status' => 'verification-link-already']);
       return redirect()->away(config('app.frontend_url').'/login?verified=1');
    }

    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
        Log::info("Email vérifié avec succès pour l'utilisateur ID: " . $user->id);
    }

   // return response()->json(['status' => 'verification-link-success']);
    return redirect()->away(config('app.frontend_url').'/login?verified=1');
}

    /**
     * Mark the authenticated user's email address as verified.
    */
//     public function __invoke(EmailVerificationRequest $request): RedirectResponse
// {
//     $user = $request->user();

//     if ($user->hasVerifiedEmail()) {
//         return redirect()->away(config('app.frontend_url').'/login?verified=1');
//     }

//     if (!$user->markEmailAsVerified()) {
//         // Log l'erreur
//         Log::error("Email verification failed for user: ".$user->id);
//         return redirect()->away(config('app.frontend_url').'/login?verified=0');
//     }

//     event(new Verified($user));
//     return redirect()->away(config('app.frontend_url').'/login?verified=1');
// }
}








// public function __invoke(EmailVerificationRequest $request): RedirectResponse
// {
//     if ($request->user()->hasVerifiedEmail()) {
//         return redirect()->intended(
//             config('app.frontend_url').'/dashboard?verified=1'
//         );
//     }

//     if ($request->user()->markEmailAsVerified()) {
//         event(new Verified($request->user()));
//     }

//     // Ajoutez un message de succès
//     return redirect()->intended(
//         config('app.frontend_url').'/dashboard?verified=1'
//     )->with('status', 'Votre email a été vérifié avec succès!');
// }