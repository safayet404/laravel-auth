<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessInterviewAiJob;
use App\Models\Interview;
use Illuminate\Http\Request;

class InterviewRecordingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

    public function upload(Request $request,Interview $interview){
        try {
            $data = $request->validate([
                'recording' => 'required|file'
            ]);

            $file = $data['recording'];

            $path = $file->store('interviews/{$interview->id}','public');

            $interview->update([
                'recording_path' => $path,
                'recording_mime' => $file->getMimeType(),
                'recording_size' => $file->getSize(),
                'status' => 'processing',
                'completed_at' => now()
            ]);

           ProcessInterviewAiJob::dispatch($interview->id);

                return response()->json(['ok' => true]);

        } catch (\Throwable $th) {
               return response()->json(['status' => 'failed', 'message' => $th->getMessage()]);

        }
    }
}
