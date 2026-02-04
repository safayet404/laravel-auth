<?php

namespace App\Services\Llm;

use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\GenerationConfig;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Gemini\Enums\ResponseMimeType;
use Illuminate\Support\Facades\Log;

class GeminiLlmProvider implements LlmProviderInterface
{
    public function generateJson(string $prompt): array
    {
        try {
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

            $model = Gemini::generativeModel(model: 'gemini-2.5-flash')
                ->withGenerationConfig(new GenerationConfig(
                    responseMimeType: ResponseMimeType::APPLICATION_JSON,
                    responseSchema: $schema
                ));

            $result = $model->generateContent($prompt);

            $raw = $result->text();
            Log::info("Gemini raw response", ['raw' => $raw]);

            $decoded = json_decode($raw, true);

            if (!is_array($decoded)) {
                Log::error("Gemini returned non-JSON", ['raw' => $raw]);
                throw new \RuntimeException("Gemini returned non-JSON");
            }

            return $decoded;
        } catch (\Throwable $e) {
            // IMPORTANT: log the whole throwable
            Log::error("Gemini LLM failed", [
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'trace' => substr($e->getTraceAsString(), 0, 2000),
            ]);

            // rethrow so the Job fails and you see it in queue logs
            throw $e;
        }
    }
}
