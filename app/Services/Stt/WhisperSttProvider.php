<?php

namespace App\Services\Stt;

use WhisperPHP\Whisper;

class WhisperSttProvider implements SttProviderInterface
{
    /**
     * Transcribe audio to text using local Whisper.
     */
    public function transcribe(string $audioAbsolutePath): array
    {
        // Initialize the Whisper service
        $whisper = new Whisper();

        // Perform the transcription
        // The library handles the heavy lifting locally
        $text = $whisper->audio($audioAbsolutePath)->toText();

        return [
            'text' => trim($text)
        ];
    }
}
