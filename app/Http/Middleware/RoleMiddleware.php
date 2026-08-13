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
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return response()->json(['message' => 'you are not authenticated'], 401);
        }

        $userRole = $request->user()->role;

        if (is_object($userRole)) {
            $userRole = $userRole->value;
        }

        if (! in_array($userRole, $roles, true)) {
            return response()->json(['message' => 'you are not authorized to access this resource'], 403);
        }

        return $next($request);
    }
}
