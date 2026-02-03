<?php

// app/Http/Controllers/InterviewRecordingController.php
namespace App\Http\Controllers;

use App\Jobs\ProcessInterviewAiJob;
use App\Jobs\ProcessQuestionAiJob;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use Illuminate\Http\Request;

class InterviewRecordingController extends Controller
{
    public function upload(Request $request, InterviewQuestion $question)
    {
        if (!$request->hasFile('recording')) {
            return response()->json([
                'error' => 'No file received',
                'content_type' => $request->header('Content-Type'),
                'files' => array_keys($request->allFiles()),
            ], 422);
        }

        $request->validate([
            'recording' => 'required|file|max:512000',
        ]);

        $file = $request->file('recording');
        $path = $file->store("interviews/{$question->interview_id}/q{$question->order_index}", 'public');

        $question->update([
            'recording_path' => $path,
            'recording_mime' => $file->getClientMimeType(),
            'recording_size' => $file->getSize(),
            'status' => 'uploaded',
            'completed_at' => now(),
        ]);
        ProcessQuestionAiJob::dispatch($question->id);


        return response()->json(['ok' => true, 'path' => $path]);
    }
}
