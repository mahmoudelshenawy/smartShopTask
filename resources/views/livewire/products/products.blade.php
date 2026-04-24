<div>
    <!-- Hero Section -->
    <div class="relative">

        <!-- Background Image -->
        <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da" class="w-full h-[400px] object-cover">

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
            <div class="text-center text-white px-4">
                <h1 class="text-4xl font-bold mb-4">
                    Welcome to smart shop
                </h1>
                <p class="text-lg">
                    Discover amazing products at the best prices
                </p>
            </div>
        </div>

    </div>
    <div class="max-w-7xl mx-auto p-4">
        <div class="m-6">
            <h2 class="text-center text-[20px] font-semibold leading-1.5">Product List</h2>
        </div>

        <div>
            <div x-data="{ focused: false }" class="relative mb-8 max-w-xl">
                {{-- Search icon --}}
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-colors duration-200"
                        :class="focused ? 'text-indigo-500' : 'text-gray-400'" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                </div>

                {{-- Input — wire:model for Livewire, x-on for Alpine --}}
                <input wire:model.live.debounce.400ms="search" x-on:focus="focused = true" x-on:blur="focused = false"
                    type="text" placeholder="Search products..."
                    class="w-full rounded-xl border py-3 pl-11 pr-10 text-sm outline-none transition-all duration-200 bg-white"
                    :class="focused
                        ?
                        'border-indigo-400 ring-2 ring-indigo-100' :
                        'border-gray-200 hover:border-gray-300'" />

                {{-- Clear button — Alpine shows/hides it, Livewire resets the value --}}
                <button x-show="$wire.search.length > 0" x-transition x-on:click="$wire.set('search', '')"
                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition"
                    type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Loading indicator --}}
                <div wire:loading wire:target="search" class="absolute inset-y-4 right-8 flex items-center">
                    <svg class="h-4 w-4 animate-spin text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                </div>
            </div>

            {{-- No results --}}
            @if ($products->isEmpty())
                <div class="py-20 text-center">
                    <p class="text-gray-400 text-sm">No products found for "<span
                            class="font-medium text-gray-600">{{ $search }}</span>"</p>
                    <button wire:click="$set('search', '')" class="mt-3 text-xs text-indigo-500 hover:underline">
                        Clear search
                    </button>
                </div>
            @else
                {{-- Grid --}}
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($products as $product)
                        <div wire:key="product-{{ $product->id }}"
                            class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                            <div class="relative h-48 overflow-hidden bg-gray-100">
                                <img src="https://placehold.co/400x300?text={{ urlencode($product->name) }}"
                                    alt="{{ $product->name }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105" />
                            </div>

                            <div class="flex flex-1 flex-col gap-2 p-4">
                                <h3 class="text-sm font-semibold text-gray-900 leading-snug line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-xs text-gray-500 line-clamp-3 flex-1">
                                    {{ $product->description }}
                                </p>
                                <div class="mt-auto flex items-center justify-between pt-3">
                                    <span class="text-base font-bold text-indigo-600">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                    <a href="{{ route('products.show', $product) }}" wire:navigate
                                        class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-indigo-700">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
