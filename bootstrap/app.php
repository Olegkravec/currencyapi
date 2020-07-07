<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

use Illuminate\Support\Arr;

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
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


if (!function_exists('array_get')) {
    function array_get($a, $k)
    {
        return Illuminate\Support\Arr::get($a, $k);
    }
}

if (!function_exists('array_collapse')) {
    function array_collapse($a)
    {
        return Illuminate\Support\Arr::collapse($a);
    }
}

if (!function_exists('array_first')) {
    function array_first($a)
    {
        return Illuminate\Support\Arr::first($a);
    }
}

if (!function_exists('array_where')) {
    function array_where($a, $callback)
    {
        return Illuminate\Support\Arr::where($a, $callback);
    }
}

if (!function_exists('array_dot')) {
    function array_dot($a)
    {
        return Illuminate\Support\Arr::dot($a);
    }
}

if (!function_exists('array_set')) {
    function array_set($a, $k, $v)
    {
        return Illuminate\Support\Arr::set($a, $k, $v);
    }
}


/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
