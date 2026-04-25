<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAiService
{
    private string $apiKey;

    private string $model = 'gpt-4.1-nano';

    private string $baseUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    public function ask(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ])
            ->timeout(15)
            ->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 256,
                'temperature' => 0,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('OpenAI API error: '.$response->body());
        }

        return $response->json('choices.0.message.content');
    }
}
