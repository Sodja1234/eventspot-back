<?php



namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Organisateur;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;
use App\Models\EmailOtp;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     description="Create a new user account with role-specific data and send an email verification notification.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","role"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="strongPassword123"),
     *             @OA\Property(property="role", type="string", enum={"organisateur", "public"}, example="public"),
     *             @OA\Property(property="nom_organis", type="string", example="Organisateur Name", description="Required if role is 'organisateur'"),
     *             @OA\Property(
     *                 property="interets",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 description="Array of interest IDs, required if role is 'public'"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registration successful. Please verify your email address."),
     *             @OA\Property(property="user", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error during registration",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred during registration."),
     *             @OA\Property(property="error", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function store(RegisterRequest $request): JsonResponse


    {
        try {
            $validated = $request->validated();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'role' => $validated['role'],
            ]);

            if ($user->role === 'organisateur') {
                Organisateur::create([
                    'user_id' => $user->id,
                    'nom_organis' => $validated['nom_organis'],
                ]);
            } elseif ($user->role === 'public') {
                $user->interets()->attach($validated['interets']);
            }


            // Génération et enregistrement de l'OTP lié à l'utilisateur
            $otp = rand(100000, 999999);

            EmailOtp::updateOrCreate(
                ['user_id' => $user->id],
                ['otp' => $otp, 'expires_at' => now()->addMinutes(10)]
            );

            $user->sendEmailVerificationNotification();


            return response()->json([
                'message' => 'Inscription réussie. Veuillez vérifier votre adresse e-mail.',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l’inscription: ' . $e->getMessage());

            return response()->json([
                'message' => 'Une erreur s’est produite lors de l’inscription.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}




// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use App\Models\User;
// use App\Models\Organisateur;
// use Illuminate\Auth\Events\Registered;
// use Illuminate\Http\Request;
// use Illuminate\Http\Response;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Validation\Rules;

// class RegisteredUserController extends Controller
// {
//     /**
//      * Handle an incoming registration request.
//      *
//      * @throws \Illuminate\Validation\ValidationException
//      */
//     public function store(Request $request): Response
//         {
//             $validated = $request->validate([
//                 'fullname' => 'required|string',
//                 'email' => 'required|email|unique:users',
//                 'password' => 'required|min:6|confirmed',
//                 'role' => 'required|in:public,organisateur',
//                 'nom_organis' => 'required_if:role,organisateur',
//                 'interets' => 'array|required_if:role,public',
//                 'interets.*' => 'exists:interets,id',
//             ]);

//             $user = User::create([
//                 'fullname' => $validated['fullname'],
//                 'email' => $validated['email'],
//                 'password' => bcrypt($validated['password']),
//                 'role' => $validated['role'],
//             ]);

//             if ($user->role === 'organisateur') {
//                 Organisateur::create([
//                     'user_id' => $user->id,
//                     'nom_organis' => $validated['nom_organis'],
//                 ]);
//             } elseif ($user->role === 'public') {
//                 $user->interets()->attach($validated['interets']);
//             }
//             $user->sendEmailVerificationNotification();
//             return response()->noContent();
//     }
// }