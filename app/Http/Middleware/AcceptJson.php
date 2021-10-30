<?php

namespace App\Http\Middleware;

use Closure;

class AcceptJson
{
    public function handle($request, Closure $next)
    {
        $request->headers->add(['Accept' => 'application/json']);
        return $next($request);
    }
}