<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Services\CartService;
use App\Services\ViewedProductsService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.client')]
#[Title('Products')]
class ProductDetails extends Component
{
    public Product $product;

    public bool $inCart = false;

    public function mount(Product $product, ViewedProductsService $viewedProducts, CartService $cart): void
    {
        $this->product = $product;
        $this->inCart = $cart->inCart($product->id);
        $viewedProducts->track($product->id);
    }

    public function toggleCart(CartService $cart)
    {
        if (! auth('web')->check()) {
            return $this->redirect(route('login'), navigate: true);
        }
        if ($this->inCart) {
            session()->flash('cart_message', 'Removed From Cart!');
            $cart->remove($this->product->id);
        } else {
            session()->flash('cart_message', 'Added to cart!');
            $cart->add($this->product->id);
        }

        $this->inCart = ! $this->inCart;
    }

    private function getRecommended(): Collection
    {
        return Product::where('id', '!=', $this->product->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.products.product-details', [
            'recommended' => $this->getRecommended(),
        ]);
    }
}
