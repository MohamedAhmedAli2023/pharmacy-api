<?php
namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Closure;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        // إذا كان الطلب يتوقع JSON، لا تعيد التوجيه
        if ($request->expectsJson()) {
            return null;
        }

        return null; // لا يوجد إعادة توجيه، لأنه API فقط
    }

    protected function unauthenticated($request, array $guards)
    {
        throw new \Illuminate\Auth\AuthenticationException(
            'Unauthenticated.', $guards, $this->jsonResponse()
        );
    }

    protected function jsonResponse()
    {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }
}

