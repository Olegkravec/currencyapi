<?php

namespace App\Http\Middleware;

use Closure;

class AddCustomHeadersAPIMiddleware
{
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
        header("Access-Control-Allow-Headers: access_token, token_type, X-RateLimit-Limit, X-RateLimit-Remaining");
        header("Access-Control-Expose-Headers: access_token, token_type, X-RateLimit-Limit, X-RateLimit-Remaining");
        return $response;
    }
}
