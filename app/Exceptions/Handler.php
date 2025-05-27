<?php

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions as BaseExceptions;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler
{
    public function __invoke(BaseExceptions $exceptions): BaseExceptions
    {
        $exceptions->render(function (HttpException $e) {
            if ($e->getStatusCode() === 419) {
                return redirect()->back()->with('error', 'Сессия истекла. Пожалуйста, попробуйте снова.');
            }

            return null;
        });

        return $exceptions;
    }
}
