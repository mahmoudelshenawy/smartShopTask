<?php

use App\Livewire\Products\ProductRecommendations;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('recommendations are returned from gemini api based on viewed products', function () {
    // Arrange — create products
    $products = Product::factory()->count(10)->create();
    $currentProduct = $products->first();

    // Simulate viewed history (last 3 excluding current)
    $viewedIds = $products->skip(1)->take(3)->pluck('id')->toArray();
    session()->put('viewed_products', $viewedIds);

    // Pick 3 products Gemini will "recommend"
    $recommendedProducts = $products->skip(4)->take(3);
    $recommendedTitles = $recommendedProducts->pluck('name')->toArray();

    // Mock the Gemini HTTP call
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => json_encode($recommendedTitles)],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    // Act
    $component = Livewire::test(ProductRecommendations::class, [
        'product' => $currentProduct,
    ]);

    // Assert — all 3 recommended titles appear in the rendered output
    foreach ($recommendedTitles as $title) {
        $component->assertSee($title);
    }

    // Assert — current product is NOT in recommendations
    $component->assertDontSee($currentProduct->name);

    // Assert — Gemini was actually called (not served from cache or fallback)
    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'generativelanguage.googleapis.com');
    });
});
