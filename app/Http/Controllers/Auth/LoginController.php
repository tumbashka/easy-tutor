<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\StatefulGuard;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyAuthenticatedSessionController;
use Laravel\Fortify\Http\Requests\LoginRequest;

class LoginController extends FortifyAuthenticatedSessionController
{
    protected $guard;

    protected AttemptToAuthenticate $authenticator;

    protected PrepareAuthenticatedSession $sessionPreparer;

    public function __construct(
        StatefulGuard $guard,
        AttemptToAuthenticate $authenticator,
        #[\SensitiveParameter]
        PrepareAuthenticatedSession $sessionPreparer
    ) {
        parent::__construct($guard);
        $this->guard = $guard;
        $this->authenticator = $authenticator;
        $this->sessionPreparer = $sessionPreparer;
    }

    public function store(LoginRequest $request)
    {
        return (new \Illuminate\Pipeline\Pipeline(app()))
            ->send($request)
            ->through([$this->authenticator, $this->sessionPreparer])
            ->then(fn ($request) => redirect()->route('home'));
    }
}
