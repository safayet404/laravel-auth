<?php

namespace App\Services\Stt;

interface SttProviderInterface
{
    public function transcribe(string $audioAbsolutePath): array; // ['text'=>...]
}
