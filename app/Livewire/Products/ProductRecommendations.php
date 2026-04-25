<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Services\ClaudeService;
use App\Services\GeminiService;
use App\Services\OpenAiService;
use App\Services\ViewedProductsService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
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

    private function getRecommended(): Collection
    {
        $viewedProducts = app(ViewedProductsService::class);
        $viewedIds = $viewedProducts->get();
        if (empty($viewedIds)) {
            return $this->randomFallback();
        }

        $cacheKey = 'recommendations_'.$this->product->id.'_'.implode('_', $viewedIds);

        try {
            $recommendedIds = Cache::remember($cacheKey, now()->addHours(2), function () use ($viewedIds) {
                return $this->fetchFromOpenAI($viewedIds)->pluck('id')->toArray();
            });

            $recommended = Product::whereIn('id', $recommendedIds)->get();

            return $recommended->count() >= 3 ? $recommended : $this->randomFallback();
        } catch (\Throwable $e) {
            report($e);

            return $this->randomFallback();
        }
    }

    private function fetchFromClaude(array $viewedIds): Collection
    {
        $allProducts = Product::query()->whereNotIn('id', $viewedIds)->get(['id', 'name']);
        $viewedTitles = Product::whereIn('id', $viewedIds)->pluck('name')->join(', ');
        $productList = $allProducts->pluck('name')->join("\n- ");

        $prompt = <<<PROMPT
You are a product recommendation engine.
The user recently viewed these products: {$viewedTitles}
From the list below, suggest exactly 3 similar or complementary products.
Return ONLY a valid JSON array of product titles exactly as they appear in the list, nothing else.
Example: ["Product A", "Product B", "Product C"]
Product list:
- {$productList}
PROMPT;

        $claude = app(ClaudeService::class);
        $text = $claude->ask($prompt);

        Log::info('the response from claude', [
            'responseText' => $text,
        ]);

        $text = preg_replace('/```json|```/', '', $text);
        $titles = json_decode(trim($text), true);

        if (! is_array($titles)) {
            throw new \RuntimeException('Claude returned unexpected format: '.$text);
        }

        return Product::whereIn('name', $titles)
            ->where('id', '!=', $this->product->id)
            ->limit(3)
            ->get();
    }

    private function fetchFromGemini(array $viewedIds): Collection
    {
        $allProducts = Product::query()->whereNotIn('id', $viewedIds)->get(['id', 'name']);
        $viewedTitles = Product::whereIn('id', $viewedIds)->pluck('name')->join(', ');
        $productList = $allProducts->pluck('name')->join("\n- ");

        $prompt = <<<PROMPT
You are a product recommendation engine.
The user recently viewed these products: {$viewedTitles}
From the list below, suggest exactly 3 similar or complementary products.
Return ONLY a valid JSON array of product titles exactly as they appear in the list, nothing else.
Example: ["Product A", "Product B", "Product C"]
Product list:
- {$productList}
PROMPT;

        $gemini = app(GeminiService::class);
        $text = $gemini->ask($prompt);

        Log::info('the response from gemini', [
            'responseText' => $text,
        ]);

        $text = preg_replace('/```json|```/', '', $text);
        $titles = json_decode(trim($text), true);

        if (! is_array($titles)) {
            throw new \RuntimeException('Gemini returned unexpected format: '.$text);
        }

        return Product::whereIn('name', $titles)
            ->where('id', '!=', $this->product->id)
            ->limit(3)
            ->get();
    }

    private function fetchFromOpenAI(array $viewedIds): Collection
    {
        $allProducts = Product::query()->whereNotIn('id', $viewedIds)->get(['id', 'name']);
        $viewedTitles = Product::whereIn('id', $viewedIds)->pluck('name')->join(', ');
        $productList = $allProducts->pluck('name')->join("\n- ");

        $prompt = <<<PROMPT
You are a product recommendation engine.

The user recently viewed these products: {$viewedTitles}

From the list below, suggest exactly 3 similar or complementary products.
Return ONLY a valid JSON array of product titles exactly as they appear in the list, nothing else.
Example: ["Product A", "Product B", "Product C"]

Product list:
- {$productList}
PROMPT;

        $openAI = app(OpenAiService::class);
        $text = $openAI->ask($prompt);

        Log::info('the response from openai', [
            'responseText' => $text,
        ]);

        $text = preg_replace('/```json|```/', '', $text);
        $titles = json_decode(trim($text), true);

        if (! is_array($titles)) {
            throw new \RuntimeException('OpenAI returned unexpected format: '.$text);
        }

        return Product::whereIn('name', $titles)
            ->where('id', '!=', $this->product->id)
            ->limit(3)
            ->get();
    }

    private function randomFallback(): Collection
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
