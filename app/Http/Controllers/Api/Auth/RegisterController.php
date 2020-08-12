<?php

namespace App\Http\Controllers\Api\Auth;

use App\User;
use App\Notifications\WelcomeToEventy;
use App\Http\Requests\StoreUserRequest;
use Symfony\Component\HttpFoundation\Response;

class RegisterController
{
    public function __invoke(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        $user->notify(new WelcomeToEventy($user));
        return response()->json(null, Response::HTTP_CREATED);
    }
}
