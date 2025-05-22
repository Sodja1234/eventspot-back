<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
{
    $user = $request->user();
    
    if ($user->hasVerifiedEmail()) {
        return redirect()->away(config('app.frontend_url').'/login?verified=1');
    }

    if (!$user->markEmailAsVerified()) {
        // Log l'erreur
        Log::error("Email verification failed for user: ".$user->id);
        return redirect()->away(config('app.frontend_url').'/login?verified=0');
    }

    event(new Verified($user));
    return redirect()->away(config('app.frontend_url').'/login?verified=1');
}
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