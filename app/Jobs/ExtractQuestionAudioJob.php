<?php

namespace App\Jobs;

use App\Models\InterviewQuestion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;

class ExtractQuestionAudioJob implements ShouldQueue
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

        if (!$q->recording_path) {
            throw new \RuntimeException("No recording_path for question {$q->id}");
        }
        $q->update(['status' => 'processing']);
        $disk = Storage::disk('public');
        $videoAbs = $disk->path($q->recording_path);
        $audioRel = "interviews/{$q->interview_id}/q{$q->order_index}/audio.wav";
        $audioAbs = $disk->path($audioRel);

        $process = new Process([
            'ffmpeg',
            '-y',
            '-i',
            $videoAbs,
            '-vn',
            '-ac',
            '1',
            '-ar',
            '16000',
            '-f',
            'wav',
            $audioAbs
        ]);

        $process->setTimeout(180);
        $process->run();

        if (!$process->isSuccessful()) {
            $q->update(['status' => 'failed']);
            throw new \RuntimeException("FFmpeg failed: " . $process->getErrorOutput());
        }

        $q->update(['audio_path' => $audioRel]);
    }
}
