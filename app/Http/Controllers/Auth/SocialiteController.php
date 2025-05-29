<?php


// app/Http/Controllers/Auth/SocialiteController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organisateur;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SocialiteController extends Controller
{



    
  
    public function redirect($provider)
    {

        
        return Socialite::driver($provider)->redirect();
    }

    
  

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // Créer un nouveau user
                $user = User::firstOrCreate([
                    'fullname' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'public', // Par défaut public
                    'email_verified_at' => now(), // Email vérifié automatiquement
                ]);
            }

            Auth::login($user);

            return redirect('/dashboard');

        } catch (\Exception $e) {

            
            return redirect('/login')->withErrors([
                'email' => 'Erreur lors de l\'authentification avec '.ucfirst($provider)
            ]);
        }
    }






      public function redirectToGoogle()
    {
        return Socialite::driver('googlee')->redirect();
    }


    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('googlee')->user();
        
        $user = User::firstOrCreate([
            'email' => $googleUser->getEmail(),
            'fullname' => $googleUser->getName(),
            'password' => Hash::make(Str::random(24)),
            'role' => 'public', // Par défaut public
            'email_verified_at' => now(), // Email vérifié automatiquement
        ]);

        Auth::login($user);

            return redirect('/dashboard');
    }

    
}