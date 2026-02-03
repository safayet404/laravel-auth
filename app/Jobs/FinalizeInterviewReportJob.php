<?php

namespace App\Jobs;

use App\Models\Interview;
use App\Models\InterviewQuestion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class FinalizeInterviewReportJob implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $questionId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $q = InterviewQuestion::findOrFail($this->questionId);
        $interview = Interview::with(['student', 'complianceProfile', 'questions'])->findOrFail($q->interview_id);

        $report = [
            'student' => [
                'name' => $interview->student->first_name . ' ' . $interview->student->last_name
            ],
            'application' => [
                'institution' => $interview->complianceProfile->institution,
                'program' => $interview->complianceProfile->program,
                'intake' => $interview->complianceProfile->intake,
            ],
            'per_question' => $interview->questions->map(fn($qq) => [
                'order_index' => $qq->order_index,
                'question' => $qq->question_text,
                'transcript' => $qq->transcript_text,
                'summary' => $qq->ai_summary_json,
                'status' => $qq->status,
            ])->values()->all(),
        ];

        $interview->update(['ai_report_json' => $report]);
    }
}
