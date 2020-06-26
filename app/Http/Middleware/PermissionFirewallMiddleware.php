<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PermissionFirewallMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission_name = "null") // 'null' is a default value assigned by Kernel.php
    {
        if($permission_name == "null" or empty($permission_name))
            return $next($request);

        if(!Auth::user()->can($permission_name)){
            flash("You dont have permission for this part")->error();
            return redirect()->back();
        }
        return $next($request);
    }
}
