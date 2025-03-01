<?php

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions as BaseExceptions;
use Throwable;

class Handler
{
    public function __invoke(BaseExceptions $exceptions): BaseExceptions
    {


        return $exceptions;
    }

}
