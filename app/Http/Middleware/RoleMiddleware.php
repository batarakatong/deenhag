<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roles = array_slice(func_get_args(), 2);
        if (! $request->user() || ! $request->user()->role || ! in_array($request->user()->role->name, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
