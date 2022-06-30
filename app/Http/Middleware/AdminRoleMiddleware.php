<?php

namespace App\Http\Middleware;

use App\Constants\AdminRole;
use Closure;
use Illuminate\Http\Request;
use JWTAuth;

class AdminRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        $user = JWTAuth::user();
        if ($user->role === AdminRole::MANAGER) {
            return $next($request);
        }
        return response()->json(['message' => 'Not access'], 401);

    }
}
