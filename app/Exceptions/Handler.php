<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (TokenExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], 401);
        });

        $this->renderable(function (TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        });

        $this->renderable(function (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        });

        $this->renderable(function (AuthenticationException $e) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return parent::render($request, $e);
    }
}
