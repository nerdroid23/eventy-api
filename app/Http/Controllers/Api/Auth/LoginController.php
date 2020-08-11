<?php

namespace App\Http\Controllers\Api\Auth;

use App\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginController
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken('web')->plainTextToken;
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
