<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function unifiedLogin(Request $request)
    {
        try {
            $email = $request->input('email');
            $password = $request->input('password');

            // 1. Check User table
            $person = User::where('email', $email)->first();
            $type = 'user';

            // 2. If not found, check Student table
            if (!$person) {
                $person = Student::where('email', $email)->first();
                $type = 'student';
            }

            // 3. CRITICAL: Check if $person exists before verifying password
            if ($person && Hash::check($password, $person->password)) {

                $token = JWTToken::CreateToken($person->email, $person->id, $type);

                return response()->json([
                    'status' => 'success',
                    'token' => $token,
                    'user_type' => $type,
                    'role' => $person->getRoleNames()->first(),
                    'user' => $person
                ], 200);
            }

            // If credentials don't match
            return response()->json(['status' => 'failed', 'message' => 'Invalid Email or Password'], 401);
        } catch (\Throwable $th) {
            // Return the actual error message so you can see it in Postman
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }
}
