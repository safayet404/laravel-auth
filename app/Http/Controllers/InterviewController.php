<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionResource;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Models\Student;
use App\Services\AiInterviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

use function Termwind\render;

class InterviewController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Interview/Index');
    }

    public function msg()
    {
        return Inertia::render('Compliance/Msg');
    }

    public function cas()
    {
        return Inertia::render('Interview/Cas');
    }

    public function setup()
    {
        return Inertia::render('Interview/Setup');
    }
    public function questions()
    {
        return Inertia::render('Interview/Questions');
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'student_id' => 'required|exists:students,id',
                'compliance_profile_id' => 'required|exists:student_compliance_profiles,id',
            ]);

            $interview = Interview::create([
                'student_id' => $data['student_id'],
                'compliance_profile_id' => $data['compliance_profile_id'],
                'counselor_user_id' => $request->user()->id,
                'status' => 'draft',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Interview Created', 'data' => $interview]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Interview $interview)
    {
        try {

            return $interview->load(['student', 'complianceProfile', 'questions', 'messages.author']);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function allInterviews()
    {
        return Interview::all();
    }

    public function interviewSession($id)
    {
        $interview = Interview::with('questions')->findOrFail($id);
        return Inertia::render("Session/Index", [
            'interviewId' => (int) $id,
            'interview' => $interview
        ]);
    }

    public function generateQuestions(Request $request, Interview $interview, AiInterviewService $ai)
    {
        try {
            $data = $request->validate(['count' => 'nullable|integer|min:1|max:12']);

            $count = $data['count'] ?? 5;

            $interview->load(['student', 'complianceProfile', 'questions']);

            $result = $ai->generateQuestions($interview, $count);

            if (!isset($result['parsed']['questions']) || !is_array($result['parsed']['questions'])) {
                return response()->json(['error' => 'AI output invalid'], 422);
            }

            DB::transaction(function () use ($interview, $result) {
                $interview->questions()->delete();

                foreach ($result['parsed']['questions'] as $idx => $q) {
                    InterviewQuestion::create([
                        'interview_id' => $interview->id,
                        'order_index' => $idx,
                        'type' => $q['type'] ?? 'short',
                        'question_text' => $q['text'] ?? '',
                        'prep_seconds' => (int)($q['prep_seconds'] ?? 12),
                        'answer_seconds' => (int)($q['answer_seconds'] ?? 45),
                        'rubric_json' => $q['rubric'] ?? null
                    ]);
                }

                $interview->ai_question_prompt = $result['prompt'];
                $interview->ai_question_raw = $result['raw'];
                $interview->status = 'ready';
                $interview->save();
            });

            return $interview->fresh()->load('questions');
        } catch (\Throwable $th) {
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()]);
        }
    }

    public function start(Request $request, Interview $interview)
    {
        if ($interview->status !== 'ready') {
            return response()->json(['error' => "Interview not ready"], 422);
        }

        $interview->update(['status' => 'in_progress', 'started_at' => now()]);
        return $interview->fresh();
    }

    public function recordUpload(Request $request)
    {
        $request->validate([
            'video' => ['required', 'file', 'max:51200'], // 50MB
            // If you want strict types:
            // 'video' => ['required','file','mimetypes:video/webm,video/mp4,video/quicktime','max:51200'],
        ]);

        // Debug (temporary): confirm Laravel sees the file
        // dd($request->file('video')->getClientMimeType(), $request->file('video')->getSize());

        $path = $request->file('video')->store('interviews', 'public');

        return response()->json([
            'message' => 'uploaded',
            'path' => $path,
            'mime' => $request->file('video')->getClientMimeType(),
            'size' => $request->file('video')->getSize(),
        ]);
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

    public function fetchQuestion(Student $student)
    {
        try {

            $interview = Interview::where('student_id', $student->id)
                ->with('questions')
                ->first();


            if (!$interview) {
                return response()->json([
                    'status' => 'failed',
                    'message' => "No interview has been initialized for Student #{$student->id}."
                ], 404);
            }



            return response()->json([
                'status' => 'success',
                'interview_status' => $interview->status, // Pulling status from the Interview model
                'data' => QuestionResource::collection($interview->questions)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
