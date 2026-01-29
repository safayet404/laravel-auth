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
        try {
            $token = $request->cookie('token');

            $result = JWTToken::VerifyToken($token);

            if ($result == "unauthorized") {
                return response()->json(['message' => 'unauthorized'], 401);
            }

            $user = ($result->userType === 'student')
                ? Student::find($result->userID)
                : User::find($result->userID);

            if (!$user) {
                return response()->json(['message' => 'user not found'], 401);
            }

            $request->setUserResolver(function () use ($user) {
                return $user;
            });
            $role = $user->getRoleNames()->first();

            $request->attributes->add([
                'email'    => $result->userEmail,
                'id'       => $result->userID,
                'user'     => $user->makeHidden('roles'),
                'role'     => $role,
                'userType' => $result->userType
            ]);

            return $next($request);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }
}
