<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role_id' => $request->role_id ?? 1, // Default role (e.g., customer)
        ]);

        $token = auth('api')->login($user);
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => auth('api')->user(),
        ]);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user()
    {
        return response()->json(auth('api')->user());
    }

    public function forgotPassword(Request $request)
    {
        // Implement password reset logic (e.g., send email)
        return response()->json(['message' => 'Password reset link sent (not implemented yet)']);
    }

    public function resetPassword(Request $request)
    {
        // Implement reset logic
        return response()->json(['message' => 'Password reset successful (not implemented yet)']);
    }
}
