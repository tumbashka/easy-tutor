<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Enums\Role;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::loginView(function () {
            return view('login.index');
        });

        Fortify::registerView(function () {
            return view(
                'registration.index',
                [
                    'roles' => [
                        'names' => [Role::Teacher->name, Role::Student->name],
                        'values' => [Role::Teacher->value, Role::Student->value],
                    ]
                ]
            );
        });

        Fortify::verifyEmailView(function () {
            return view('login.email-verify');
        });

        Fortify::resetPasswordView(function () {
            return view('login.reset-password');
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('login.forgot');
        });

        Fortify::authenticateUsing(function (LoginRequest $request) {
            $credentials = $request->only('email', 'password');
            $remember = $request->only('remember');

            if (auth()->attempt($credentials, $remember)) {
                $request->session()->regenerate();

                return auth()->user();
            }

            return null;
        });

        $this->app->instance(
            \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class,
            new LoginController(
                app(StatefulGuard::class),
                app(\Laravel\Fortify\Actions\AttemptToAuthenticate::class),
                app(\Laravel\Fortify\Actions\PrepareAuthenticatedSession::class)
            )
        );

        $this->app->instance(
            \Laravel\Fortify\Http\Controllers\RegisteredUserController::class,
            new RegisteredUserController(
                app(StatefulGuard::class)
            )
        );

        Event::forget(\Illuminate\Auth\Events\Registered::class);

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
