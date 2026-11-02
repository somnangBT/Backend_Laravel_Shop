<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/products/search')) {
        return $next($request);
    }

        $allowedStaff = ['admin', 'manager', 'cashier', 'kitchen'];
        if (!Auth::check() || !in_array(Auth::user()->role, $allowedStaff)) {
            // For API requests, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied. Staff privileges required.'
                ], 403);
            }
            
            // For web requests, redirect with error
            Auth::logout();
            return redirect()->route('login')->with('error', 'Access denied. Staff privileges required.');
        }

        return $next($request);
    }
}
