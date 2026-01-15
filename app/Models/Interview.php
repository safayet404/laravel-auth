<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $fillable = [
        'student_id','counselor_user_id','compliance_profile_id','status','ai_question_prompt','ai_question_raw','recording_path','recording_mime','recording_size','transcript_text','ai_report_json','report_path','started_at','completed_at'
    ];

    protected $casts = ['ai_report_json' => 'array','started_at' => 'datetime', 'completed_at' => 'datetime'];

    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function complianceProfile() {
        return $this->belongsTo(StudentComplianceProfile::class,'compliance_profile_id');
    }
    public function questions(){
        return $this->hasMany(InterviewQuestion::class)->orderBy('order_index');
    }
    public function messages(){
        return $this->hasMany(ComplianceMessage::class)->latest();
    }


}
