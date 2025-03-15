<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = auth('api')->user();
        if (!$user || $user->role_id != $role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $next($request);
    }
}
