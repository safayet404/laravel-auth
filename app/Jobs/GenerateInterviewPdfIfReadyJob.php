<?php

namespace App\Jobs;

use App\Models\Interview;
use App\Models\InterviewQuestion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;


class GenerateInterviewPdfIfReadyJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public int $questionId) {}

    public function handle(): void
    {
        $q = InterviewQuestion::findOrFail($this->questionId);
        $interview = Interview::with(['student', 'complianceProfile', 'questions'])->findOrFail($q->interview_id);

        $allDone = $interview->questions->every(fn($qq) => $qq->status === 'completed');
        if (!$allDone) return;

        $data = [
            'interview' => $interview,
            'report' => $interview->toArray(),  // <-- this includes student, compliance_profile, questions, updated_at, etc.
        ];

        $pdf = Pdf::loadView('reports.interview_per_question', $data);
        $rel = "interviews/{$interview->id}/report.pdf";
        Storage::disk('public')->put($rel, $pdf->output());

        $interview->update(['report_path' => $rel]);
    }
}
