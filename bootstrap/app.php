<?php

use Illuminate\Foundation\Application;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The application instance that serves as the IoC container and router is
| created here. We keep this file minimal and compatible with Laravel 10
| by returning the application instance expected by `artisan` and the
| framework.
|
*/

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// Vercel: redirect storage ke /tmp karena filesystem read-only
if (getenv('VERCEL') || isset($_ENV['VERCEL'])) {
    $tmpStorage = '/tmp/laravel';
    $app->useStoragePath($tmpStorage);
}

/*
| Bind important interfaces to the container so the framework can resolve
| the HTTP kernel, console kernel and exception handler.
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

return $app;
