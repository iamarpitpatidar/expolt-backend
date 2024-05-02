<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            return $next($request);
        }

        return response()->json(['status' => 'error', 'message' => 'Access Denied'], 403);
    }
}
