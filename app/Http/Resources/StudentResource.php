<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StudentResource extends JsonResource
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
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email,
            'dob' => $this->dob,
            'documents' => $this->documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'file_name' => $doc->original_name,
                    'file_type' => $doc->mime,
                    'file_size' => round($doc->size / 1024, 2) . ' KB',
                    'url' => $doc->path,

                    'uploaded_at' => $this->created_at->toIso8601String()
                ];
            }),
            // 'interviews' => $this->interviews,
            'profiles' => $this->complianceProfiles->map(function ($profile) {
                return [
                    'profile_id' => $profile->id,
                    'institution' => $profile->institution,
                    'program' => $profile->program,
                    'intake' => $profile->intake,
                    'tuition_fee' => $profile->tuition_fee,
                    'scholarship' => $profile->scholarship,
                    'paid_amount' => $profile->paid_amount,
                    'remaining_amount' => $profile->remaining_amount,
                    'notes' => $profile->notes,
                    'counselor_name' => $profile->counselor->name ?? "N/A",
                    'counselor_email' => $profile->counselor->email ?? "N/A",
                ];
            }),
            'interviews' => $this->interviews->map(function ($interview) {
                return [
                    'id' => $interview->id,
                    'status' => $interview->status,
                    'created_at' => $interview->created_at->toIso8601String(),
                    // Map the questions for this specific interview
                    'questions' => $interview->questions->map(function ($question) {
                        return [
                            'id' => $question->id,
                            'text' => $question->question_text,
                            'status' => $question->status,
                            'type' => $question->type
                        ];
                    })
                ];
            }),
            'created_at' => $this->created_at->toIso8601String(),

        ];
    }
}
