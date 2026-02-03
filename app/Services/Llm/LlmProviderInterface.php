<?php

// app/Services/Llm/LlmProviderInterface.php
namespace App\Services\Llm;

interface LlmProviderInterface
{
    public function generateJson(string $prompt): array;
}
