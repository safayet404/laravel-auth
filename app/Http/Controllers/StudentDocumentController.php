<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Http\Request;

class StudentDocumentController extends Controller
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
    public function store(Request $request,Student $student)
    {
        try {
          $request->validate([
                'files' => 'required|array',
                "files.*" => 'required|file|max:10240',
            ]);

            $uploadedDocs = [];

            foreach($request->file('files') as $file)
            {
                $path = $file->store("students/{$student->id}/docs",'public');
                $uploadedDocs[] = StudentDocument::create([
                'student_id' => $student->id,
                'uploaded_by_user_id' => $request->user()->id,
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);
            }

            return response(['status' => 'success','message' => "Documents Uploaded",'data' => $uploadedDocs]);
        } catch (\Throwable $th) {
return response(['status' => 'failed','message' => $th->getMessage()]);

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
