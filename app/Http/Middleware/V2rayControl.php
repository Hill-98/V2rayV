<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class V2rayControl
{
    private $exception = [
        "subscribe/update/"
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod("get")) {
            return $next($request);
        }
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);
        $content = $response->getContent();
        if (!empty($response)) {
            $json = json_decode($content, true);
            $uri = Str::after(Str::finish($request->path(), "/"), "api/");
            if ($json && !empty($json["success"]) && !in_array($uri, $this->exception)) {
                event("V2rayControl");
            }
        }
        return $response;
    }
}
