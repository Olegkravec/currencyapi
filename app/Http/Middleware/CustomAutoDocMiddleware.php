<?php

namespace App\Http\Middleware;

use App\Services\CustomSwaggerService;
use Closure;
use RonasIT\Support\AutoDoc\Http\Middleware\AutoDocMiddleware;
use RonasIT\Support\AutoDoc\Services\SwaggerService;

class CustomAutoDocMiddleware extends AutoDocMiddleware
{
    protected $service;
    public static $skipped = false;

    public function __construct()
    {
        $this->service = app(CustomSwaggerService::class);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ((config('app.env') == 'testing') && !self::$skipped) {
            $this->service->addData($request, $response);
        }

        self::$skipped = false;

        return $response;
    }
}
