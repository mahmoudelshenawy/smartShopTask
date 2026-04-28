<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\RecommendationService;
use App\Services\ViewedProductsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductsRecommendationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_basic_products_list(): void
    {

        $category = Category::create(['name' => 'Test']);
        $viewedProduct = Product::create([
            'name' => 'Viewed Product',
            'category_id' => $category->id,
        ]);
        $this->assertDatabaseHas('categories', ['name' => 'Test']);
        $this->assertDatabaseHas('products', [
            'name' => 'Viewed Product',
            'category_id' => $category->id,
        ]);
    }

    public function test_recommendation_3_products()
    {
        Artisan::call('db:seed');
        $products_count = Product::query()->count();
        $viewProductService = app(ViewedProductsService::class);
        $viewProductService->track(1);
        $viewProductService->track(5);
        $viewProductService->track(8);
        $viewProductService->track(16);
        $viewProductService->track(23);

        $recommendationService = app(RecommendationService::class)->setProduct(Product::find(1));

        $fetchFromOpenAI = $recommendationService->fetchFromOpenAI($viewProductService->get());
        if ($fetchFromOpenAI->count() < 3) {
            $fetchFromOpenAI = $recommendationService->randomFallback();
        }
        $this->assertEquals('3', $fetchFromOpenAI->count());
        $this->assertEquals('3', count($viewProductService->get()));
        $this->assertEquals(30, $products_count);
    }
}
