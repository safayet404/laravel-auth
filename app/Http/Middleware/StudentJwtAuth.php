<?php

namespace App\Http\Middleware;

use App\Helper\JWTToken;
use App\Models\Student;
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

            if (!$token) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Unauthorized'
                ], 401);
            }

            $decoded = JWTToken::VerifyToken($token);

            if (!$decoded || empty($decoded->studentID)) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Invalid token'

                ], 401);
            }


            $student = Student::select('id', 'first_name', 'last_name', 'email')->find($decoded->studentID);

            if (!$student) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'User not found'
                ], 401);
            }

            $request->attributes->set('student', $student);



            return $next($request);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized'
            ], 401);
        }
    }
}
