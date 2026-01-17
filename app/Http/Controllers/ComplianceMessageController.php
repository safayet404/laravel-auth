<?php

namespace App\Http\Controllers;

use App\Models\ComplianceMessage;
use App\Models\Interview;
use Illuminate\Http\Request;

class ComplianceMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Interview $interview)
    {
        return $interview->messages()->with('author')->paginate(50);
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
    public function store(Request $request,Interview $interview)
    {
        try {
            $data = $request->validate([
           
            'message' => 'required|string'
        ]);

         $msg = ComplianceMessage::create([
            'interview_id' => $interview->id,
            'author_user_id' => $request->user()->id,
            'message' => $data['message']
        ]);

        return response()->json(['status' => 'success', 'message' => "Compliance Message Created Successfully",'data' => $msg]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()]);
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
