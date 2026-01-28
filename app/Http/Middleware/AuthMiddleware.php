<?php

namespace App\Http\Middleware;

use App\Helper\JWTToken;
use App\Models\Student;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('token');

        $result = JWTToken::VerifyToken($token);

        if ($result == "unauthorized") {
            return response()->json(['message' => 'unauthorized'], 401);
        }

        if ($result->userType === 'student') {
            $user = Student::find($result->userID);
        } else {
            $user = User::find($result->userID);
        }

        $request->attributes->add(['email' => $result->userEmail, 'id' => $result->userID, 'user' => $user]);

        return $next($request);
    }
}
