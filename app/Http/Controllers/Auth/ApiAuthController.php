<?php

// app/Http/Controllers/Auth/ApiAuthController.php

// app/Http/Controllers/Auth/ApiAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;

class ApiAuthController extends Controller
{
    
    

    public function login(LoginRequest $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
 try{
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        
        
        $token = $user->createToken('token')->plainTextToken;
        $user['token'] = $token;

        return response()->json([
            'data' => $user,
        ], 201);

    }
    catch (\Exception $exception) {
        return response()->json([
            'error' => [
                $exception->getMessage()
            ]
        ], 500);
    }


}
public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

}