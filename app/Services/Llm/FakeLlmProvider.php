<?php

namespace App\Services\Llm;

class FakeLlmProvider implements LlmProviderInterface
{
    public function generateJson(string $prompt): array
    {
        return [
            'key_points' => ['STUB key point'],
            'concerns' => [],
            'consistency' => 'unclear'
        ];
    }
}
