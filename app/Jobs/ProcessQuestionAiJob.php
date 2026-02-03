<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;

class ProcessQuestionAiJob implements ShouldQueue
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
        Bus::chain([
            new ExtractQuestionAudioJob($this->questionId),
            new TranscribeQuestionJob($this->questionId),
            new SummarizeQuestionJob($this->questionId),
            new FinalizeInterviewReportJob($this->questionId),
            new GenerateInterviewPdfIfReadyJob($this->questionId),
        ])->dispatch();
    }
}
