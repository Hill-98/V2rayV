<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthenticate
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
//        if (app()->environment("production")) {
//            if ($request->header("api_token") !== env("API_TOKEN")) {
//                abort(403);
//            }
//        }
        return $next($request);
    }
}
