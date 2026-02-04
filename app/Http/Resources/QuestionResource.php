<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'status' => $this->status,
            'interview_id' => $this->interview_id,
            'prep_seconds' => $this->prep_seconds,
            'answer_seconds' => $this->answer_seconds,
            'question_text' => $this->question_text,
        ];
        return parent::toArray($request);
    }
}
