<?php

namespace App\Jobs;

use App\Models\InterviewQuestion;
use App\Services\Llm\LlmProviderInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class SummarizeQuestionJob implements ShouldQueue
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
    public function handle(LlmProviderInterface $llm): void
    {
        $q = InterviewQuestion::with('interview.complianceProfile', 'interview.student')->findOrFail($this->questionId);
        $i = $q->interview;

        $prompt = "Return STRICT JSON only with keys: key_points(array), concerns(array), consistency('ok'|'unclear'|'concerning').
Question: {$q->question_text}
Transcript: {$q->transcript_text}
Context: Institution={$i->complianceProfile->institution}, Program={$i->complianceProfile->program}, Intake={$i->complianceProfile->intake}";

        $summary = $llm->generateJson($prompt);

        $q->update([
            'ai_summary_json' => $summary,
            'status' => 'completed',
        ]);
    }
}
