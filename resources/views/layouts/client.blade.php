<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SmartShop</title>
    @include('partials.head')
</head>

<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">

            <!-- Logo -->
            <div class="text-xl font-bold text-gray-800">
                <a href="{{ route('products.index') }}" wire:navigate>
                    Smart Shop
                </a>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-4">

                @auth
                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" wire:navigate class="text-gray-600 hover:text-black">
                        🛒 Cart
                    </a>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="bg-red-500 text-white px-4 py-1 rounded hover:bg-red-600">
                            Logout
                        </button>
                    </form>
                @endauth

                @guest
                    <!-- Login -->
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-black">
                        Login
                    </a>

                    <!-- Signup -->
                    <a href="{{ route('register') }}" class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">
                        Sign Up
                    </a>
                @endguest

            </div>
        </div>
    </nav>

    <!-- Livewire Content -->
    <div>
        {{ $slot ?? '' }}
    </div>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
