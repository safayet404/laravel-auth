<?php

namespace App\Services\Llm;

use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\GenerationConfig;
use Gemini\Data\Schema; // Import the Schema class
use Gemini\Enums\DataType; // Import DataType enum
use Gemini\Enums\ResponseMimeType; // Import ResponseMimeType enum

class GeminiLlmProvider implements LlmProviderInterface
{
    public function generateJson(string $prompt): array
    {
        // Define the Schema using the dedicated Data Class
        $schema = new Schema(
            type: DataType::OBJECT,
            properties: [
                'key_points' => new Schema(
                    type: DataType::ARRAY,
                    items: new Schema(type: DataType::STRING)
                ),
                'concerns' => new Schema(
                    type: DataType::ARRAY,
                    items: new Schema(type: DataType::STRING)
                ),
                'consistency' => new Schema(
                    type: DataType::STRING,
                    enum: ['ok', 'unclear', 'concerning']
                ),
            ],
            required: ['key_points', 'concerns', 'consistency']
        );

        // Call the model (Updating to gemini-2.5-flash for 2026 stability)
        $result = Gemini::generativeModel(model: 'gemini-2.5-flash')
            ->withGenerationConfig(new GenerationConfig(
                // Use the Enum here to fix the "Expected type" error
                responseMimeType: ResponseMimeType::APPLICATION_JSON,
                responseSchema: $schema
            ))
            ->generateContent($prompt);

        return json_decode($result->text(), true) ?? [];
    }
}
