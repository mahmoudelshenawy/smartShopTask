<?php

namespace App\Livewire\Cart;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.client')]
class Cart extends Component
{
    public bool $showCheckout = false;

    public string $cardName = 'John Doe';

    public string $cardNumber = '4203 3027 0804 7524';

    public string $expiry = '02/28';

    public string $cvv = '123';

    public bool $paid = false;

    public function increment(int $productId, CartService $cart): void
    {
        $cart->incrementQuantity($productId);
    }

    public function decrement(int $productId, CartService $cart): void
    {
        $cart->decrementQuantity($productId);
    }

    public function remove(int $productId, CartService $cart): void
    {
        $cart->remove($productId);
    }

    public function clearCart(CartService $cart): void
    {
        $cart->clear();
    }

    public function openCheckout(): void
    {
        $this->showCheckout = true;
    }

    public function closeCheckout(): void
    {
        $this->showCheckout = false;
        $this->resetForm();
    }

    public function confirmPayment(CartService $cart): void
    {
        // $this->validate([
        //     'cardName'   => 'required|string|min:3',
        //     'cardNumber' => 'required|digits:16',
        //     'expiry'     => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
        //     'cvv'        => 'required|digits:3',
        // ], [
        //     'cardNumber.digits' => 'Card number must be 16 digits.',
        //     'expiry.regex'      => 'Expiry must be in MM/YY format.',
        //     'cvv.digits'        => 'CVV must be 3 digits.',
        // ]);

        $cart->clear();
        $this->paid = true;
        $this->showCheckout = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->cardName = '';
        $this->cardNumber = '';
        $this->expiry = '';
        $this->cvv = '';
    }

    private function getCartItems(CartService $cart): Collection
    {
        $cartData = $cart->get(); // [productId => quantity]

        if (empty($cartData)) {
            return collect();
        }

        $products = Product::whereIn('id', array_keys($cartData))->get()->keyBy('id');

        return collect($cartData)->map(function ($quantity, $productId) use ($products) {
            $product = $products->get($productId);

            return $product ? (object) [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $product->price * $quantity,
            ] : null;
        })->filter();
    }

    public function render(CartService $cart)
    {
        $items = $this->getCartItems($cart);
        // dd($items);
        $total = $items->sum('subtotal');

        return view('livewire.cart.cart', compact('items', 'total'));
    }
}
