<?php

use App\Console\Handler as ScheduleHandler;
use App\Exceptions\Handler as ExceptionHandler;
use App\Http\Middleware\Handler as MiddlewareHandler;
use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(new MiddlewareHandler)
    ->withExceptions(new ExceptionHandler)
    ->withSchedule(new ScheduleHandler)
    ->withCommands([__DIR__.'/../app/Console/Commands'])
    ->withRouting(
        web: [
            __DIR__.'/../routes/web/common.php',
            __DIR__.'/../routes/web/teacher.php',
            __DIR__.'/../routes/web/student.php',
            __DIR__.'/../routes/web/admin.php',
            __DIR__.'/../routes/web/statistic.php',
        ],
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
    )->create();
