<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->validated();
        Passport::personalAccessTokensExpireIn(now()->addHour(10));
        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken($user->id);

            return response()->json([
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token->accessToken,
                    'expires_at' => $token->token->expires_at,
                ],
            ]);
        }
    }
}
