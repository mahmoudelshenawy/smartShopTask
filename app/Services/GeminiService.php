<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    private string $apiKey;

    private string $model = 'gemini-2.0-flash';

    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function ask(string $prompt): string
    {
        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->timeout(15)
            ->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 256,
                    'temperature' => 0,
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Gemini API error: '.$response->body());
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        if ($text === null) {
            throw new \RuntimeException('Gemini returned empty response: '.$response->body());
        }

        return $text;
    }
}
