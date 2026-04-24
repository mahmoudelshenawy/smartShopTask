<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Services\ViewedProductsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.client')]
class ProductRecommendations extends Component
{
    public Product $product;
    public string $error = '';

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="mt-14">
            <h2 class="mb-6 text-lg font-bold text-gray-900">Recommended For You</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                @foreach (range(1, 3) as $i)
                    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden animate-pulse">
                        <div class="h-40 bg-gray-200"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-3 bg-gray-200 rounded w-full"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/2 mt-2"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        HTML;
    }

    private function getRecommended(): \Illuminate\Database\Eloquent\Collection
    {
        $viewedProducts = app(ViewedProductsService::class);
        $viewedIds      = $viewedProducts->getExcluding($this->product->id);

        if (empty($viewedIds)) {
            return $this->randomFallback();
        }

        // Unique cache key per viewed combination + current product
        $cacheKey = 'recommendations_' . $this->product->id . '_' . implode('_', $viewedIds);

        try {
            $recommendedIds = Cache::remember($cacheKey, now()->addHours(2), function () use ($viewedIds) {
                return $this->fetchFromGemini($viewedIds)->pluck('id')->toArray();
            });

            $recommended = Product::whereIn('id', $recommendedIds)->get();

            return $recommended->count() >= 3 ? $recommended : $this->randomFallback();
        } catch (\Throwable $e) {
            report($e);
            return $this->randomFallback();
        }
    }

    private function fetchFromClaude(array $viewedIds): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('id', '<=', 0)->get();

        $allProducts   = Product::all(['id', 'name']);
        $viewedTitles  = Product::whereIn('id', $viewedIds)->pluck('name')->join(', ');
        $productList   = $allProducts->pluck('name')->join("\n- ");

        $prompt = <<<PROMPT
        You are a product recommendation engine.
        
        The user recently viewed these products: {$viewedTitles}
        
        From the list below, suggest exactly 3 similar or complementary products.
        Return ONLY a valid JSON array of product titles exactly as they appear in the list, nothing else.
        Example: ["Product A", "Product B", "Product C"]
        
        Product list:
        - {$productList}
        PROMPT;

        $response = Http::withHeaders([
            'x-api-key'         => config('services.claude.key'),
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-haiku-4-5-20251001', // fast + cheap for this use case
            'max_tokens' => 256,
            'messages'   => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('Claude API request failed: ' . $response->status());
        }

        $text = $response->json('content.0.text');

        Log::info("the response from claude", [
            'response' => $response->json(),
            'responseText' => $text,
        ]);
        // Strip any accidental markdown fences just in case
        $text  = preg_replace('/```json|```/', '', $text);
        $titles = json_decode(trim($text), true);

        if (!is_array($titles)) {
            throw new \RuntimeException('Claude returned unexpected format: ' . $text);
        }

        // Match returned titles back to real DB records (case-insensitive)
        return Product::whereIn('name', $titles)
            ->where('id', '!=', $this->product->id)
            ->limit(3)
            ->get();
    }
    private function fetchFromGemini(array $viewedIds): \Illuminate\Database\Eloquent\Collection
    {
        // return Product::where('id', '<=', 0)->get();
        $allProducts  = Product::all(['id', 'name']);
        $viewedTitles = Product::whereIn('id', $viewedIds)->pluck('name')->join(', ');
        $productList  = $allProducts->pluck('name')->join("\n- ");

        $prompt = <<<PROMPT
    You are a product recommendation engine.

    The user recently viewed these products: {$viewedTitles}

    From the list below, suggest exactly 3 similar or complementary products.
    Return ONLY a valid JSON array of product titles exactly as they appear in the list, nothing else.
    Example: ["Product A", "Product B", "Product C"]

    Product list:
    - {$productList}
    PROMPT;

        $response = Http::post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . config('services.gemini.key'),
            [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature'     => 0.3, // low = more predictable/structured output
                    'maxOutputTokens' => 256,
                ],
            ]
        );

        if ($response->failed()) {
            throw new \RuntimeException('Gemini API request failed: ' . $response->status());
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        // Strip accidental markdown fences
        $text   = preg_replace('/```json|```/', '', $text);
        $titles = json_decode(trim($text), true);

        if (!is_array($titles)) {
            throw new \RuntimeException('Gemini returned unexpected format: ' . $text);
        }

        return Product::whereIn('name', $titles)
            ->where('id', '!=', $this->product->id)
            ->limit(3)
            ->get();
    }
    private function randomFallback(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('id', '!=', $this->product->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.products.product-recommendations', [
            'recommended' => $this->getRecommended(),
        ]);
    }
}
