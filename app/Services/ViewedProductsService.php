<?php

namespace App\Services;

class ViewedProductsService
{
    private const SESSION_KEY = 'viewed_products';

    private const MAX_ITEMS = 3;

    public function track(int $productId): void
    {
        $viewed = $this->get();

        $viewed = array_filter($viewed, fn ($id) => $id !== $productId);
        $viewed = array_values($viewed);
        array_unshift($viewed, $productId);

        $viewed = array_slice($viewed, 0, self::MAX_ITEMS);

        session()->put(self::SESSION_KEY, $viewed);
    }

    public function get(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    public function getExcluding(int $productId): array
    {
        return array_filter(
            $this->get(),
            fn ($id) => $id !== $productId
        );
    }
}
