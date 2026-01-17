<?php

namespace App\Jobs;

use App\Models\Interview;
use App\Services\AiInterviewService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessInterviewAiJob implements ShouldQueue
{
    use Dispatchable, Queueable,InteractsWithQueue,SerializesModels ;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $interviewId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AiInterviewService $ai): void
    {
        $interview = Interview::with('questions')->findOrFail($this->interviewId);

        $result = $ai->transcribeAndReport($interview);

        $interview->update([
              'transcript_text' => $result['transcript'] ?? null,
      'ai_report_json'  => $result['report'] ?? null,
      'status' => 'completed',
        ]);

        // TODO: generate PDF/DOCX report and set report_path

    }
}
