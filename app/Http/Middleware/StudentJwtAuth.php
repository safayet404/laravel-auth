<?php

namespace App\Http\Middleware;

use App\Helper\JWTToken;
use App\Models\Student;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentJwtAuth
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
            $decoded = JWTToken::VerifyToken($token);

            if ($decoded === 'unauthorized' || empty($decoded->userID)) {
                return response()->json(['status' => 'failed', 'message' => 'Unauthorized'], 401);
            }

            // Branching logic based on token type
            if ($decoded->userType === 'student') {
                $person = Student::find($decoded->userID);
            } else {
                $person = User::find($decoded->userID);
            }

            if (!$person) {
                return response()->json(['status' => 'failed', 'message' => 'User not found'], 401);
            }

            // Set attributes for use in Controllers
            $request->attributes->set('auth_user', $person);
            $request->attributes->set('auth_type', $decoded->userType);

            return $next($request);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'failed', 'message' => 'Unauthorized'], 401);
        }
    }
}
