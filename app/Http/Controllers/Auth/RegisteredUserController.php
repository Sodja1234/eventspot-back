<?php



namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organisateur;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
        {
            $validated = $request->validate([
                'fullname' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed',
                'role' => 'required|in:public,organisateur',
                'nom_organis' => 'required_if:role,organisateur',
                'interets' => 'array|required_if:role,public',
                'interets.*' => 'exists:interets,id',
            ]);
        
            $user = User::create([
                'fullname' => $validated['fullname'],
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
            $user->sendEmailVerificationNotification();
            return response()->noContent();
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