<?php

namespace App\Services;

use App\Models\Interview;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\GenerationConfig;
use Gemini\Data\Schema; // Add this
use Gemini\Enums\ResponseMimeType; // Add this
use Gemini\Enums\DataType; // Add this if using strict types

class AiInterviewService
{
    // public function generateQuestions(Interview $interview,int $count =  5)   {
    //     $profile = $interview->complianceProfile;
    //     $student = $interview->student;

    //         $prompt = "Generate {$count} interview questions for student {$student->first_name} {$student->last_name} . 

    //     Institution : {$profile->program}
    //     Intake : {$profile->intake}
    //     Tuition : {$profile->tuition_fee}
    //     Scholarship : {$profile->scholarship}
    //     Paid : {$profile->paid_amount} 
    //     Remaining : {$profile->remaining_amount}
    //    Return strict JSON: {\"questions\":[{\"type\":\"short\",\"prep_seconds\":12,\"answer_seconds\":45,\"text\":\"...\"}]} ";

    //    $parsed = [
    //     'questions' => collect(range(1,$count))->map(function($i) use ($profile) {
    //         return [
    //             'type' => 'short',
    //             'prep_seconds' => 2,
    //             'answer_seconds' => 3,
    //             'text' => "why did you choose {$profile->program} at {$profile->institution} ?",
    //             'rubric' => ['clarity' => 1, 'consistency' => 1]
    //         ];
    //     })->all()
    //    ];

    //    $raw = json_encode($parsed,JSON_UNESCAPED_UNICODE);
    //    return ['prompt' => $prompt, 'raw' => $raw, 'parsed' => $parsed];




    // }

    public function generateQuestions(Interview $interview, int $count = 5): array
    {
        $profile = $interview->complianceProfile;
        $student = $interview->student;

        // 1. Build a high-context prompt
        $prompt = "Generate {$count} interview questions for a student compliance interview.
        Student: {$student->first_name} {$student->last_name}
        Institution: {$profile->institution}, Program: {$profile->program}
        Intake: {$profile->intake}, Tuition: {$profile->tuition_fee}
        Scholarship: {$profile->scholarship}, Paid: {$profile->paid_amount}
        Remaining: {$profile->remaining_amount}
        
        Focus on: Genuine intention to study, financial capability, and course knowledge.";

        // 2. Define the JSON Schema (Forces Gemini to follow your structure)
        $schema = new Schema(
            type: DataType::OBJECT,
            properties: [
                'questions' => new Schema(
                    type: DataType::ARRAY,
                    items: new Schema(
                        type: DataType::OBJECT,
                        properties: [
                            'type' => new Schema(type: DataType::STRING),
                            'prep_seconds' => new Schema(type: DataType::INTEGER),
                            'answer_seconds' => new Schema(type: DataType::INTEGER),
                            'text' => new Schema(type: DataType::STRING),
                            'rubric' => new Schema(
                                type: DataType::OBJECT,
                                properties: [
                                    'clarity' => new Schema(type: DataType::INTEGER),
                                    'consistency' => new Schema(type: DataType::INTEGER),
                                ]
                            )
                        ],
                        required: ['type', 'prep_seconds', 'answer_seconds', 'text']
                    )
                )
            ],
            required: ['questions']
        );

        // 3. Call the API
        $result = $result = Gemini::generativeModel(model: 'gemini-2.5-flash')
            ->withGenerationConfig(new GenerationConfig(
                // Use the Enum here instead of a string
                responseMimeType: ResponseMimeType::APPLICATION_JSON,
                responseSchema: $schema
            ))
            ->generateContent($prompt);

        $raw = $result->text();
        $parsed = json_decode($raw, true);

        return [
            'prompt' => $prompt,
            'raw' => $raw,
            'parsed' => $parsed
        ];
    }


    public function transcribeAndReport(Interview $interview)
    {

        // TODO: call STT + summariziation here

        return [
            'transcript' => "STUB transcript. Replace with speech-to-text output",
            'report' => [
                'summary' => ['STUB summary bullet 1', 'STUB summary bullet 2'],
                'per_question' => $interview->questions->map(fn($q) => [
                    'question' => $q->question_text,
                    'transcript' => "STUB answer transcript",
                    'summary' => ['STUB answer summary'],
                ])->all()
            ]
        ];
    }
}
