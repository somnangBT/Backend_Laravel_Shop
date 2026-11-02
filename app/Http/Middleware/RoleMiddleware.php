<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated.'
                ], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has one of the allowed roles
        if (!empty($roles) && !$user->hasRole($roles)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied. You do not have permission for this section.'
                ], 403);
            }

            // Redirect kitchen to kitchen screen, cashier to orders, etc.
            if ($user->role === 'kitchen') {
                return redirect()->route('orders.kitchen')->with('error', 'Access restricted to Kitchen Display.');
            }

            return redirect()->route('dashboard')->with('error', 'Access denied. Insufficient role permissions.');
        }

        return $next($request);
    }
}
