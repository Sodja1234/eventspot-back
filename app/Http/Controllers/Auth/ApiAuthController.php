<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            // Authentifier l'utilisateur via la méthode personnalisée
            $request->authenticate();

            $user = User::with('interets')->where('email', $request->email)->firstOrFail();

            if (is_null($user->email_verified_at)) {
                return response()->json([
                    'message' => 'Votre adresse e-mail n\'a pas encore été vérifiée.',
                ], 403);
            }

            $token = $user->createToken('token')->plainTextToken;
            $user['token'] = $token;
            $data = UserResource::make($user);

            return response()->json([
                'message' => 'Connexion réussie',
                'data' => $data,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Échec de la connexion',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }
}













// app/Http/Controllers/Auth/ApiAuthController.php

// app/Http/Controllers/Auth/ApiAuthController.php
















// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Validation\ValidationException;
// use App\Http\Requests\Auth\LoginRequest;
// use App\Models\User;

// class ApiAuthController extends Controller
// {



//     public function login(LoginRequest $request)
//     {
//         $request->validate([
//             'email' => 'required|email',
//             'password' => 'required',
//         ]);
//  try{
//         if (!Auth::attempt($request->only('email', 'password'))) {
//             throw ValidationException::withMessages([
//                 'email' => ['The provided credentials are incorrect.'],
//             ]);
//         }

//         $user = User::where('email', $request->email)->firstOrFail();



//         $token = $user->createToken('token')->plainTextToken;
//         $user['token'] = $token;

//         return response()->json([
//             'data' => $user,
//         ], 201);

//     }
//     catch (\Exception $exception) {
//         return response()->json([
//             'error' => [
//                 $exception->getMessage()
//             ]
//         ], 500);
//     }


// }
// public function logout(Request $request)
//     {
//         $request->user()->currentAccessToken()->delete();

//         return response()->json(['message' => 'Logged out successfully']);
//     }

// }