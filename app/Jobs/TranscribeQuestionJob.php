<?php

namespace App\Jobs;

use App\Models\InterviewQuestion;
use App\Services\Stt\SttProviderInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class TranscribeQuestionJob implements ShouldQueue
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
    public function handle(SttProviderInterface $stt): void
    {
        $q = InterviewQuestion::findOrFail($this->questionId);

        if (!$q->audio_path) throw new \RuntimeException("No audio_path for question {$q->id}");

        $audioAbs = Storage::disk('public')->path($q->audio_path);
        $res = $stt->transcribe($audioAbs);

        $q->update([
            'transcript_text' => $res['text'] ?? null,
        ]);
    }
}
