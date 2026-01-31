<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'created_at' => $this->created_at->format('Y-m-d h:i A'),

        ];
    }
}
