<?php

namespace App\Services\Stt;

class FakeSttProvider implements SttProviderInterface
{
    public function transcribe(string $audioAbsolutePath): array
    {
        return ['text' => 'STUB transcript - replace with real STT provider.'];
    }
}
