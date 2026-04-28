<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Services\RecommendationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.client')]
class ProductRecommendations extends Component
{
    public Product $product;

    public string $error = '';

    protected RecommendationService $recommendation;

    public function mount(Product $product, RecommendationService $recommendation): void
    {
        $this->product = $product;
        $this->recommendation = $recommendation;
        $this->recommendation->setProduct($product);
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

    public function render()
    {
        return view('livewire.products.product-recommendations', [
            'recommended' => $this->recommendation->getRecommended(),
        ]);
    }
}
