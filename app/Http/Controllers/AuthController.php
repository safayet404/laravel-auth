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

            $person = User::where('email', $email)->first();
            $type = 'user';

            if (!$person) {
                $person = Student::where('email', $email)->first();
                $type = 'student';
            }

            if ($person && Hash::check($password, $person->password)) {

                $token = JWTToken::CreateToken($person->email, $person->id, $type);

                return response()->json([
                    'status' => 'success',
                    'token' => $token,
                    'user_type' => $type,
                    'role' => $person->getRoleNames()->first(),
                    'user' => $person
                ])->cookie(
                    'token',
                    $token,
                    60 * 24 * 30,
                    '/',
                    null,
                    true,
                    true,
                    false,
                    'None'
                );;
            }

            return response()->json(['status' => 'failed', 'message' => 'Invalid Email or Password'], 401);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = $request->attributes->get('user');

            return response()->json([
                'status' => 'success',
                'user' => $user
            ]);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
