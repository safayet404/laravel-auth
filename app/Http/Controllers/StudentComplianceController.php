<?php

namespace App\Http\Controllers;

use App\Http\Resources\ComplianceResource;
use App\Models\Student;
use App\Models\StudentComplianceProfile;
use Illuminate\Http\Request;

use function PHPSTORM_META\map;

class StudentComplianceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $peer_page = $request->query('peer_page', 10);
            $profiles = StudentComplianceProfile::with('student', 'counselor', 'interviews.questions')->latest()->paginate($peer_page);

            return ComplianceResource::collection($profiles);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function fetchCompliance()
    {
        try {
            $profiles = StudentComplianceProfile::with('student:id,first_name,last_name,email')
                ->select('id', 'student_id', 'institution')
                ->get();

            $data = $profiles->map(function ($profile) {
                return [
                    'id' => $profile->id,
                    'student_id' => $profile->student_id,
                    'name' => $profile->student->first_name .  ' ' . $profile->student->first_name,
                    'email' => $profile->student->email,
                    'institution' => $profile->institution,
                ];
            });
            return response()->json(['message' => 'success', 'data'    => $data]);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function test(Request $request)
    {
        try {
            $peer_page = $request->query('peer_page', 10);
            $profiles = StudentComplianceProfile::with('student', 'counselor', 'interviews.questions')->latest()->paginate($peer_page);

            return response()->json($profiles);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Student $student)
    {
        try {
            $data = $request->validate([
                'institution' => 'required|string|max:255',
                'program' => 'required|string|max:255',
                'intake' => 'required|string|max:100',
                'tuition_fee' => 'required|numeric|min:0',
                'scholarship' => 'required|numeric|min:0',
                'paid_amount' => 'required|numeric|min:0',
                'remaining_amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:5000',
            ]);



            $profile = StudentComplianceProfile::create($data + [
                'student_id' => $student->id,
                'counselor_user_id' => $request->user()->id
            ]);

            return response()->json(['status' => 'success', 'message' => 'Student Compliance profile created', 'data' => $profile]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 500);
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
