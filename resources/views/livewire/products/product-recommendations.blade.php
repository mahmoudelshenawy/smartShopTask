<div class="mt-14">
    <h2 class="mb-6 text-lg font-bold text-gray-900">Recommended For You</h2>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        @forelse ($recommended as $item)
            <a href="{{ route('products.show', $item) }}" wire:navigate
                class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                <div class="h-40 overflow-hidden bg-gray-100">
                    <img src="https://placehold.co/400x300?text={{ urlencode($item->name) }}" alt="{{ $item->name }}"
                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105" />
                </div>
                <div class="flex flex-col gap-1 p-4">
                    <h3 class="text-sm font-semibold text-gray-900 line-clamp-2">{{ $item->name }}</h3>
                    <p class="text-xs text-gray-500 line-clamp-2">{{ $item->description }}</p>
                    <span class="mt-2 text-sm font-bold text-indigo-600">
                        ${{ number_format($item->price, 2) }}
                    </span>
                </div>
            </a>
        @empty
            <p class="text-sm text-gray-400 col-span-3">No recommendations available.</p>
        @endforelse
    </div>
</div>
