<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;



class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render("Student/Index", [
            'students' => Student::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("Student/Create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'nullable|email|max:255',
                'dob' => 'nullable|date',
                'password' => 'nullable|string|min:6',
            ]);

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $student = Student::create($data);

            return response()->json(['status' => 'success', 'message' => 'Student Created', 'data' => $student]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()]);
        }
    }

    public function studentLogin(Request $request)
    {
        try {
            $email = $request->input('email');
            $password = $request->input('password');

            $student = Student::where('email', $email)->select('id', 'password')->first();

            if ($student && Hash::check($password, $student->password)) {
                $token = JWTToken::CreateToken($email, $student->id);
                return response()->json([
                    'status' => 'success',
                    'message' => "User Login Successfull",
                    'token' => $token
                ])->cookie('token', $token, 60 * 24 * 30);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'unauthorized'
                ]);
            }
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
