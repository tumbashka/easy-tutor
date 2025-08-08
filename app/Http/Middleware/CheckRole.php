<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (auth()->check() && $request->user()->is_admin) {
            return $next($request);
        }

        if (auth()->check() && $request->user()->role->value === $role) {
            return $next($request);
        }

        if (auth()->check()) {
            switch ($request->user()->role) {
                case Role::Student:
                    return redirect()->route('student.home');
                case Role::Teacher:
                    return redirect()->route('schedule.index');
            }
        }

        abort(403, 'Данное действие не разрешено вашей роли' . $request->user()->role->value);
    }
}
