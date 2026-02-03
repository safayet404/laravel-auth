<?php

namespace App\Services\Llm;

use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\GenerationConfig;

class GeminiLlmProvider implements LlmProviderInterface
{
    public function generateJson(string $prompt): array
    {
        // Define the JSON schema to force compliance
        $schema = [
            'type' => 'object',
            'properties' => [
                'key_points' => ['type' => 'array', 'items' => ['type' => 'string']],
                'concerns' => ['type' => 'array', 'items' => ['type' => 'string']],
                'consistency' => ['type' => 'string', 'enum' => ['ok', 'unclear', 'concerning']],
            ],
            'required' => ['key_points', 'concerns', 'consistency']
        ];

        $result = Gemini::generativeModel(model: 'gemini-1.5-flash')
            ->withGenerationConfig(new GenerationConfig(
                responseMimeType: 'application/json',
                responseSchema: $schema
            ))
            ->generateContent($prompt);

        return json_decode($result->text(), true) ?? [];
    }
}
