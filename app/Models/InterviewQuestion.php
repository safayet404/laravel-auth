<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewQuestion extends Model
{
    protected $fillable = ['interview_id', 'order_index', 'type', 'question_text', 'prep_seconds', 'answer_seconds', 'rubric_json', 'recording_path', 'recording_mime', 'recording_size', 'status', 'transcript_text', 'started_at', 'completed_at'];

    protected $casts = ['rubric_json' => 'array'];

    public function interviews()
    {
        return $this->belongsTo(Interview::class);
    }
}
