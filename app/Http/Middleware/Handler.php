<?php

namespace App\Http\Middleware;

use App\Http\Middleware;

use Illuminate\Foundation\Configuration\Middleware as BaseMiddleware;
use Illuminate\Support\Facades\Log;

class Handler
{
    protected array $aliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin' => \App\Http\Middleware\Admin::class,
        'active' => \App\Http\Middleware\ActiveUser::class,
    ];

    public function __invoke(BaseMiddleware $middleware): BaseMiddleware
    {

        if ($this->aliases) {
            $middleware->alias($this->aliases);
        }

        return $middleware;
    }
}
