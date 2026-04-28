<?php

namespace App\Services;

class CartService
{
    private const SESSION_KEY = 'added_to_cart';

    public function add(int $productId): void
    {
        $cart = $this->get();

        if (! isset($cart[$productId])) {
            $cart[$productId] = 1;
            session()->put(self::SESSION_KEY, $cart);
            session()->save();
        }
    }

    public function remove(int $productId): void
    {
        $cart = $this->get();
        unset($cart[$productId]);
        session()->put(self::SESSION_KEY, $cart);
        session()->save();
    }

    public function incrementQuantity(int $productId): void
    {
        $cart = $this->get();
        if (isset($cart[$productId])) {
            $cart[$productId]++;
            session()->put(self::SESSION_KEY, $cart);
            session()->save();
        }
    }

    public function decrementQuantity(int $productId): void
    {
        $cart = $this->get();
        if (isset($cart[$productId]) && $cart[$productId] > 1) {
            $cart[$productId]--;
            session()->put(self::SESSION_KEY, $cart);
            session()->save();
        }
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function get(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    public function inCart(int $productId): bool
    {
        return isset($this->get()[$productId]);
    }

    public function count(): int
    {
        return array_sum($this->get());
    }
}
