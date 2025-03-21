<?php

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions as BaseExceptions;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler
{
    public function __invoke(BaseExceptions $exceptions): BaseExceptions
    {
        $exceptions->render(function (HttpException $e) {
            if ($e->getStatusCode() === 419) {
                \Log::info('CSRF token mismatch intercepted'); // Для отладки
                return redirect()->back()->with('error', 'Сессия истекла. Пожалуйста, попробуйте снова.');
            }
            // Для других HTTP-исключений передаем дальше
            return null;
        });

        return $exceptions;
    }

}
