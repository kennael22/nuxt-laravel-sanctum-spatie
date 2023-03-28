<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        // $credentials = $request->validate([
        //     'username' => ['required'],
        //     'password' => ['required'],
        // ]);
        $user = User::with(['roles' => fn ($q) => $q->with('permissions')])->where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                ['The provided credentials are incorrect.'],
                // 'username' => ['The provided credentials are incorrect.'],
                // 'password' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        // if ($user->is_active == 0) {
        //     throw ValidationException::withMessages([
        //         ['Account locked'],
        //     ]);
        // }
        
        // if ($user->roles->count() < 1) {
        //     throw ValidationException::withMessages([
        //         ['This account does not have any roles; please contact the administrator.'],
        //     ]);
        // }
        
        // $fetch = DB::select('select tokenable_id,token from personal_access_tokens');
        // $token = PersonalAccessToken::where('token', $fetch[0]->token)->first();
        // $token = DB::table('personal_access_tokens')
        // ->join('users', 'users.id', '=', 'personal_access_tokens.tokenable_id')
        // ->select('personal_access_tokens.tokenable_id', 'personal_access_tokens.token', 'users.*')
        // ->get();
        // dd($request);
        if (Auth('web')->attempt($request->toArray())) {
            // $this->authUser = $user;
            // if (auth()->user()->id != 1) {
            //     return response([
            //         'token' => $user->createToken($request->username, ['read'])->plainTextToken,
            //         'user'=>$user
            //     ],201);
            // }
            return response([
                // 'token' => $user->createToken($request->username)->plainTextToken,
                'access_token' => $user->createToken($request->email)->plainTextToken,
                'user'=>$user
            ],201);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        // $request->session()->invalidate();

        // $request->session()->regenerateToken();
        $request->user()->currentAccessToken()->delete();

        // return response()->noContent();
        // auth('sanctum')->user()->tokens()->delete();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        // $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'user log out'
        ], 200);
    }

    public function me() {
        return response()->json([
            auth()->user
        ], 200);
    }
}
