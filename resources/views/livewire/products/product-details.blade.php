<div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">

    {{-- Flash message --}}
    @if (session()->has('cart_message'))
        <div class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700 border border-green-200">
            ✓ {{ session('cart_message') }}
        </div>
    @endif

    {{-- Product Detail Card --}}
    <div class="grid grid-cols-1 gap-10 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm lg:grid-cols-2">

        {{-- Image --}}
        <div class="overflow-hidden rounded-xl bg-gray-100">
            <img src="https://placehold.co/600x500?text={{ urlencode($product->name) }}" alt="{{ $product->name }}"
                class="h-full w-full object-cover" />
        </div>

        {{-- Info --}}
        <div class="flex flex-col justify-center gap-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 leading-snug">
                    {{ $product->name }}
                </h1>
                <p class="mt-1 text-sm text-gray-400">#{{ $product->id }}</p>
            </div>

            <p class="text-sm text-gray-600 leading-relaxed">
                {{ $product->description }}
            </p>

            <div class="text-3xl font-extrabold text-indigo-600">
                ${{ number_format($product->price, 2) }}
            </div>

            <button wire:click="toggleCart('{{ \Str::uuid() }}')"
                class="flex items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-semibold transition active:scale-95 w-full sm:w-fit
        {{ $inCart
            ? 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100'
            : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                @if ($inCart)
                    {{-- Remove icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Remove from Cart
                @else
                    {{-- Add icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Add to Cart
                @endif
            </button>
        </div>
    </div>

    {{-- Recommended For You --}}
    {{-- <div class="mt-14">
        <h2 class="mb-6 text-lg font-bold text-gray-900">Recommended For You</h2>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ($recommended as $item)
                <a href="{{ route('products.show', $item) }}" wire:navigate
                    class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                    <div class="h-40 overflow-hidden bg-gray-100">
                        <img src="https://placehold.co/400x300?text={{ urlencode($item->name) }}"
                            alt="{{ $item->name }}"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105" />
                    </div>

                    <div class="flex flex-col gap-1 p-4">
                        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2">
                            {{ $item->name }}
                        </h3>
                        <p class="text-xs text-gray-500 line-clamp-2">
                            {{ $item->description }}
                        </p>
                        <span class="mt-2 text-sm font-bold text-indigo-600">
                            ${{ number_format($item->price, 2) }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div> --}}
    <livewire:products.product-recommendations :product="$product" lazy />
</div>
