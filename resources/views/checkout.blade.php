<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Add this line -->
    <title>Checkout - Ledok Sambi Ecopark</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <header>
        <div>
            <div class="container bg-[#108482] rounded-b-3xl shadow-lg">
                <img src="{{ asset('images/Logo Ledok Sambi.png') }}" alt="logo" class="h-20 object-cover mx-auto py-2" />
            </div>
        </div>
    </header>

    <div class="container mx-auto mt-5">
        <h1 class="text-3xl font-bold mb-6">Checkout</h1>
        @if(!empty($cartData['items']))
        @foreach($cartData['items'] as $item)
        <div class="grid grid-cols-2 items-center border border-gray-200 rounded-lg shadow-lg mb-5 p-4">
            <img class="object-cover rounded-lg" src="https://dummyimage.com/600x400/000/fff" alt="">
            <div class="flex flex-col justify-between p-4">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $item['name'] }}</h5>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Rp. {{ number_format($item['price'], 0, ',', '.') }}</p>
                <form class="max-w-xs mx-auto">
                    <div class="relative flex items-center gap-2">
                        <button type="button" id="decrement-button" data-input-counter-decrement="quantity-input">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="red" class="size-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>

                        </button>
                        <input type="text" id="quantity-input" data-input-counter aria-describedby="helper-text-explanation" class="bg-gray-50 border-gray-200 h-11 text-center text-gray-900 text-sm block w-full py-2.5 rounded-lg" value="{{$item['quantity']}}" required />
                        <button type="button" id="increment-button" data-input-counter-increment="quantity-input">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="blue" class="size-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>

                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

        <div class="bg-white p-4 rounded-lg shadow-lg border border-gray-200 mb-5">
            <div class="flex justify-between">
                <h3 class="text-lg font-semibold">Total Price</h3>
                <h3 class="text-lg font-bold">Rp. {{ number_format($cartData['totalPrice'], 0, ',', '.') }}</h3>
            </div>
        </div>
        @else
        <p class="text-red-500">Your cart is empty. Please add items to your cart first.</p>
        @endif

        <div class="bg-white p-4 rounded-lg shadow-lg border border-gray-200">
            <div class="flex justify-between">
                <h3 class="text-lg font-semibold">Choose Payment Method</h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </div>
        </div>
    </div>
</body>

</html>