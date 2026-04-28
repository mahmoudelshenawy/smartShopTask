<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    private ?Product $product;

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getRecommended(): Collection
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

    public function randomFallback(): Collection
    {
        if (! $this->product) {
            throw new Exception(__('current product was not set'));
        }

        return Product::where('id', '!=', $this->product->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();
    }

    public function fetchFromOpenAI(array $viewedIds): Collection
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
}
