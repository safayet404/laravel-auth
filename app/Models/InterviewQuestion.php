<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewQuestion extends Model
{
    protected $fillable = ['interview_id','order_index','type','question_text','prep_seconds','answer_seconds','rubric_json'];

    protected $casts = ['rubric_json' => 'array'];

    public function interviews(){
        return $this->belongsTo(Interview::class);
    }
}
