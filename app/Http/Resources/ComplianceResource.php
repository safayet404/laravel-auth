<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplianceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'institution' => $this->institution,
            'program' => $this->program,
            'intake' => $this->intake,
            'tuition_fee' => $this->tuition_fee,
            'scholarship' => $this->scholarship,
            'paid_amount' => $this->paid_amount,
            'remaining_amount' => $this->remaining_amount,
            'notes' => $this->notes,

            'counselor_name' => $this->counselor->name ?? "N/A",
            'counselor_email' => $this->counselor->email ?? "N/A",

            'student' =>  [
                'student_id' => $this->student->id,
                'full_name' => $this->student->first_name . ' ' . $this->student->last_name,
                'email' => $this->student->email
            ],

            'interviews' => $this->interviews->map(function ($interview) {
                return [
                    'interview_id' => $interview->id,
                    'status' => $interview->status,
                    'created_at' => $interview->created_at->toIso8601String(),
                    // Map the questions for this specific interview
                    'questions' => $interview->questions->map(function ($question) {
                        return [
                            'question_id' => $question->id,
                            'text' => $question->question_text,
                            'status' => $question->status,
                            'type' => $question->type
                        ];
                    })
                ];
            }),
            'documents' => $this->student->documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'file_name' => $doc->original_name,
                    'file_type' => $doc->mime,
                    'file_size' => round($doc->size / 1024, 2) . ' KB',
                    'url' => $doc->path,

                    'uploaded_at' => $this->created_at->toIso8601String()
                ];
            }),

            'created_at' => $this->created_at->toIso8601String(),


        ];
    }
}
