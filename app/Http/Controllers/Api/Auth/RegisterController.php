<?php

namespace App\Http\Controllers\Api\Auth;

use App\User;
use App\Http\Requests\StoreUserRequest;
use Symfony\Component\HttpFoundation\Response;

class RegisterController
{
    public function __invoke(StoreUserRequest $request)
    {
        User::create($request->validated());
        return response()->json(null, Response::HTTP_CREATED);
    }
}
