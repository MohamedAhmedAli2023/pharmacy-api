<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check() || !Auth::user()->hasRole($role)) {
            return response()->json([
                'message' => 'Unauthorized: Insufficient permissions',
                'error' => 'Role required: ' . $role
            ], 403);
        }
        return $next($request);
    }
}
