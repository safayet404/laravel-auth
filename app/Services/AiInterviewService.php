<?php

namespace App\Services;

use App\Models\Interview;

class AiInterviewService 
{
    public function generateQuestions(Interview $interview,int $count =  5)   {
        $profile = $interview->complianceProfile;
        $student = $interview->student;

            $prompt = "Generate {$count} interview questions for student {$student->first_name} {$student->last_name} . 
        
        Institution : {$profile->program}
        Intake : {$profile->intake}
        Tuition : {$profile->tuition_fee}
        Scholarship : {$profile->scholarship}
        Paid : {$profile->paid_amount} 
        Remaining : {$profile->remaining_amount}
       Return strict JSON: {\"questions\":[{\"type\":\"short\",\"prep_seconds\":12,\"answer_seconds\":45,\"text\":\"...\"}]} ";

       $parsed = [
        'questions' => collect(range(1,$count))->map(function($i) use ($profile) {
            return [
                'type' => 'short',
                'prep_seconds' => 2,
                'answer_seconds' => 3,
                'text' => "why did you choose {$profile->program} at {$profile->institution} ?",
                'rubric' => ['clarity' => 1, 'consistency' => 1]
            ];
        })->all()
       ];

       $raw = json_encode($parsed,JSON_UNESCAPED_UNICODE);
       return ['prompt' => $prompt, 'raw' => $raw, 'parsed' => $parsed];



    
    }


    public function transcribeAndReport(Interview $interview) {
        
        // TODO: call STT + summariziation here

        return [
            'transcript' => "STUB transcript. Replace with speech-to-text output",
            'report' => [
                'summary' => ['STUB summary bullet 1','STUB summary bullet 2'],
                'per_question' => $interview->questions->map(fn($q) => [
                    'question' => $q->question_text,
                    'transcript' => "STUB answer transcript",
                    'summary' => ['STUB answer summary'],
                ])->all()
            ]
                ];
    }
}