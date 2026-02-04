<?php

namespace App\Jobs;

use App\Models\InterviewQuestion;
use App\Services\Llm\LlmProviderInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SummarizeQuestionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $questionId)
    {
        // We only pass the ID to avoid serialization issues
    }

    /**
     * Execute the job.
     */
    public function handle(LlmProviderInterface $llm): void
    {
        // 1. Initial heartbeat log
        Log::info("SummarizeQuestionJob: Processing Question ID {$this->questionId}");

        try {
            // 2. Load relationships safely
            $q = InterviewQuestion::with(['interview.complianceProfile', 'interview.student'])
                ->find($this->questionId);

            if (!$q) {
                Log::error("SummarizeQuestionJob: Question ID {$this->questionId} not found in database.");
                return;
            }

            // 3. Validate Transcript (Stop if empty)
            if (empty($q->transcript_text)) {
                Log::warning("SummarizeQuestionJob: No transcript text found for ID {$this->questionId}. Skipping AI summary.");
                $q->update(['status' => 'failed_missing_transcript']);
                return;
            }

            $interview = $q->interview;
            $profile = $interview?->complianceProfile;

            // 4. Build context safely using null-safe operators
            $institution = $profile?->institution ?? 'Unknown Institution';
            $program = $profile?->program ?? 'Unknown Program';
            $studentName = $interview?->student?->first_name ?? 'Student';

            $prompt = "As a compliance officer, analyze this interview answer.
            Student: {$studentName}
            Institution: {$institution}
            Program: {$program}
            Question Asked: {$q->question_text}
            Transcript: {$q->transcript_text}

            Return STRICT JSON with keys: key_points(array), concerns(array), consistency('ok'|'unclear'|'concerning').";

            // 5. Call LLM Service
            Log::info("SummarizeQuestionJob: Sending request to Gemini for ID {$this->questionId}");

            $summary = $llm->generateJson($prompt);

            // 6. Update database
            $q->update([
                'ai_summary_json' => $summary,
                'status' => 'completed',
            ]);

            Log::info("SummarizeQuestionJob: Successfully summarized ID {$this->questionId}");
        } catch (\Throwable $e) {
            Log::error("SummarizeQuestionJob FAILED for ID {$this->questionId}: " . $e->getMessage(), [
                'exception' => $e
            ]);

            // Re-throw the exception so the queue knows it failed and can retry
            throw $e;
        }
    }
}
