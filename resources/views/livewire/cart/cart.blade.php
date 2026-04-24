<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">

    <h1 class="mb-8 text-2xl font-bold text-gray-900">Your Cart</h1>

    {{-- Success message after payment --}}
    @if ($paid)
        <div class="mb-8 rounded-2xl bg-green-50 border border-green-200 p-8 text-center">
            <div class="text-4xl mb-3">🎉</div>
            <h2 class="text-lg font-bold text-green-800">Payment Confirmed!</h2>
            <p class="mt-1 text-sm text-green-600">
                Thank you for your order. Your cart has been cleared.
            </p>
            <a href="{{ route('products.index') }}"
                class="mt-4 inline-block rounded-xl bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-700 transition">
                Continue Shopping
            </a>
        </div>
    @elseif ($items->isEmpty())
        {{-- Empty cart --}}
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-16 text-center">
            <div class="text-5xl mb-4">🛒</div>
            <p class="text-gray-500 text-sm">Your cart is empty.</p>
            <a href="{{ route('products.index') }}"
                class="mt-4 inline-block rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                Browse Products
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

            {{-- Items list --}}
            <div class="lg:col-span-2 flex flex-col gap-4">
                @foreach ($items as $item)
                    <div wire:key="cart-item-{{ $item->product->id }}"
                        class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                        {{-- Image --}}
                        <img src="https://placehold.co/80x80?text={{ urlencode($item->product->name) }}"
                            alt="{{ $item->product->name }}"
                            class="h-20 w-20 rounded-xl object-cover bg-gray-100 shrink-0" />

                        {{-- Info --}}
                        <div class="flex flex-1 flex-col gap-1 min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">
                                {{ $item->product->name }}
                            </h3>
                            <p class="text-xs text-gray-400">
                                ${{ number_format($item->product->price, 2) }}
                            </p>
                            <p class="text-sm font-bold text-indigo-600">
                                ${{ number_format($item->subtotal, 2) }}
                            </p>
                        </div>

                        {{-- Quantity controls --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <button wire:click="decrement({{ $item->product->id }})" @disabled($item->quantity <= 1)
                                class="h-8 w-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition flex items-center justify-center text-lg font-medium">−</button>

                            <span class="w-6 text-center text-sm font-semibold text-gray-800">
                                {{ $item->quantity }}
                            </span>

                            <button wire:click="increment({{ $item->product->id }})"
                                class="h-8 w-8 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition flex items-center justify-center text-lg font-medium">+</button>
                        </div>

                        {{-- Remove --}}
                        <button wire:click="remove({{ $item->product->id }})"
                            wire:confirm="Remove this item from your cart?"
                            class="ml-2 shrink-0 text-gray-300 hover:text-red-500 transition" title="Remove">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                @endforeach

                {{-- Clear all --}}
                <div class="flex justify-end">
                    <button wire:click="clearCart" wire:confirm="Clear your entire cart?"
                        class="text-xs text-gray-400 hover:text-red-500 transition underline underline-offset-2">
                        Clear all items
                    </button>
                </div>
            </div>

            {{-- Order summary --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm h-fit sticky top-6">
                <h2 class="text-sm font-bold text-gray-900 mb-4">Order Summary</h2>

                <div class="flex flex-col gap-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Items ({{ $items->sum('quantity') }})</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Shipping</span>
                        <span class="text-green-600 font-medium">Free</span>
                    </div>
                    <div
                        class="border-t border-gray-100 mt-2 pt-2 flex justify-between font-bold text-gray-900 text-base">
                        <span>Total</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <button wire:click="openCheckout"
                    class="mt-6 w-full rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white hover:bg-indigo-700 active:scale-95 transition">
                    Proceed to Checkout
                </button>
            </div>
        </div>
    @endif

    {{-- Checkout Modal --}}
    @if ($showCheckout)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
            wire:click.self="closeCheckout">
            <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">

                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Payment Details</h2>
                    <button wire:click="closeCheckout" class="text-gray-400 hover:text-gray-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Fake card UI --}}
                <div class="mb-6 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 p-5 text-white shadow-lg">
                    <p class="text-xs opacity-70 mb-4">Simulated Card</p>
                    <p class="text-lg font-mono tracking-widest mb-4">
                        {{ $cardNumber ? chunk_split(str_pad($cardNumber, 16, '•'), 4, ' ') : '•••• •••• •••• ••••' }}
                    </p>
                    <div class="flex justify-between text-sm">
                        <span>{{ $cardName ?: 'YOUR NAME' }}</span>
                        <span>{{ $expiry ?: 'MM/YY' }}</span>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    {{-- Card Name --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Name on Card</label>
                        <input wire:model.live="cardName" type="text" placeholder="John Doe"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                        @error('cardName')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Card Number --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Card Number</label>
                        <input wire:model.live="cardNumber" type="text" maxlength="16" placeholder="1234567890123456"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                        @error('cardNumber')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Expiry + CVV --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Expiry</label>
                            <input wire:model.live="expiry" type="text" placeholder="MM/YY" maxlength="5"
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                            @error('expiry')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">CVV</label>
                            <input wire:model.live="cvv" type="password" placeholder="•••" maxlength="3"
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                            @error('cvv')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Total reminder --}}
                    <div
                        class="rounded-lg bg-gray-50 px-4 py-3 flex justify-between text-sm font-semibold text-gray-800">
                        <span>Total to pay</span>
                        <span class="text-indigo-600">${{ number_format($total, 2) }}</span>
                    </div>

                    <button wire:click="confirmPayment" wire:loading.attr="disabled"
                        class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white hover:bg-indigo-700 active:scale-95 transition disabled:opacity-60">
                        <span wire:loading.remove>Confirm Payment</span>
                        <span wire:loading>Processing...</span>
                    </button>

                    <p class="text-center text-xs text-gray-400">
                        🔒 This is a simulated payment. No real charge will occur.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
