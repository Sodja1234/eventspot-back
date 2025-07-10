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
    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="loginUser",
     *      tags={"Authentication"},
     *      summary="User Login",
     *      description="Logs in a user and returns an API token.",
     *      @OA\RequestBody(
     *          required=true,
     *          description="User credentials",
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email", example="kopp@odc.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login successful",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Connexion réussie"),
     *              @OA\Property(property="data", ref="#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Email not verified",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Votre adresse e-mail n'a pas encore été vérifiée.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error / Login failed",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Échec de la connexion"),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/logout",
     *      operationId="logoutUser",
     *      tags={"Authentication"},
     *      summary="User Logout",
     *      description="Logs out the current authenticated user by revoking the token.",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Logout successful",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Déconnexion réussie.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

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