<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewQuestion extends Model
{
    protected $fillable = ['interview_id', 'order_index', 'type', 'question_text', 'prep_seconds', 'answer_seconds', 'rubric_json', 'recording_path', 'recording_mime', 'recording_size', 'status', 'transcript_text', 'started_at', 'completed_at', 'audio_path', 'ai_summary_json'];

    protected $casts = [
        'rubric_json' => 'array',
        'ai_summary_json' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }
}
