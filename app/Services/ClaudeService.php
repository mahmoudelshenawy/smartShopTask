<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ClaudeService
{
    private string $apiKey;

    private string $model = 'claude-haiku-4-5-20251001';

    private string $baseUrl = 'https://api.anthropic.com/v1/messages';

    private string $apiVersion = '2023-06-01';

    public function __construct()
    {
        $this->apiKey = config('services.claude.key');
    }

    public function ask(string $prompt): string
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => $this->apiVersion,
            'Content-Type' => 'application/json',
        ])
            ->timeout(15)
            ->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 256,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Claude API error: '.$response->body());
        }

        $text = $response->json('content.0.text');

        if ($text === null) {
            throw new \RuntimeException('Claude returned empty response: '.$response->body());
        }

        return $text;
    }
}
