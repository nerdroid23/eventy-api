<?php

namespace App\Http\Controllers\Api\Auth;

use App\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Notifications\WelcomeToEventy;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginController
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json($user->createToken('web')->plainTextToken);
    }

    public function redirectToProvider(string $provider)
    {
        $url = Socialite::driver($provider)
            ->scopes(['read:user'])
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        return response()->json($url);
    }

    public function handleProviderCallback(string $provider)
    {
        $socialiteUser = Socialite::driver($provider)
            ->stateless()
            ->user();

        $user = $this->createOrGetUserForProvider($socialiteUser, $provider);

        optional($user->tokens())->delete();

        if ($user->wasRecentlyCreated) {
            $user->notify(new WelcomeToEventy($user));
        }

        return response()->json($user->createToken('web')->plainTextToken);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function createOrGetUserForProvider(\Laravel\Socialite\Contracts\User $socialiteUser, $provider)
    {
        return User::query()
            ->firstOrCreate(
                ['email' => $socialiteUser->getEmail()],
                [
                    'name' => $socialiteUser->getName(),
                    'email' => $socialiteUser->getEmail(),
                    'provider_id' => $socialiteUser->getId(),
                    'provider' => $provider,
                ]
            );
    }
}
