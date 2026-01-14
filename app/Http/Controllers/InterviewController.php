<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

         $request->validate([
        
    'video' => ['required', 'file', 'mimetypes:video/webm,video/mp4,application/octet-stream', 'max:512000'],


            'student_id' => ['nullable'],
            'question_id' => ['nullable']
        ]);

        $file = $request->file('video');
        $path = $file->store('interviews','public');

        return response()->json([
            'message' => 'Uploaded',
            'path' => $path,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize()
        ]);

        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

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
