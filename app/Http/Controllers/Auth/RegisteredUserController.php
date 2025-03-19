<?php

namespace App\Http\Controllers\Auth;

use Laravel\Fortify\Http\Controllers\RegisteredUserController as FortifyRegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\StatefulGuard;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\RegisterResponse;

class RegisteredUserController extends FortifyRegisteredUserController
{
    public function __construct(StatefulGuard $guard)
    {
        parent::__construct($guard);
    }

    public function store(Request $request, CreatesNewUsers $creator): RegisterResponse
    {
        $user = $creator->create($request->all());
        $this->guard->login($user);

        return new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect()->route('home');
            }
        };
    }
}
