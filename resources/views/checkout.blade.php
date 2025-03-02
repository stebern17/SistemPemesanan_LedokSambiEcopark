<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Add this line -->
    <title>Checkout - Ledok Sambi Ecopark</title>
    <script>
        window.csrf_token = "{{ csrf_token() }}"; // Add this line
    </script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    @vite(['resources/css/app.css', 'resources/js/checkout.js'])
</head>

<body>
    <header>
        <div>
            <div class="container bg-[#108482] rounded-b-3xl shadow-lg">
                <img src="{{ asset('images/Logo Ledok Sambi.png') }}" alt="logo" class="h-20 object-cover mx-auto py-2" />
            </div>
        </div>
    </header>

    <div class="my-5 px-4 flex gap-2">
        <a href="{{ route('welcome')}}">
            <button>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#108482" class="size-6 hover:opacity-50 active:opacity-100">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </button>
        </a>
        <p class="text-gray-700 font-semibold">Kembali</p>
    </div>

    <div>
        <div class="px-2 mt-2 flex gap-2 text-center">
            <h3 class="font-semibold p-4">You're in table :</h3>
            <p class="bg-[#108482] text-white py-4 px-5 rounded-lg drop-shadow-lg">{{($cartData['tableNumber'])}}</p>
        </div>
    </div>



    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-6">Checkout</h1>
        @if(!empty($cartData['items']))
        @foreach($cartData['items'] as $index => $item)
        <div id='itemOnCart' class="grid grid-cols-2 items-center border border-gray-200 rounded-lg shadow-lg mb-5 p-4" data-name="{{ $item['name'] }}" data-price="{{ $item['price'] }}" data-quantity="{{ $item['quantity'] }}">
            <img class="object-cover rounded-lg w-full max-h-32" src="{{ asset('storage/'. @$item['image']) }}" alt="{{ ucfirst(@$item['name']) }}">
            <div class=" flex flex-col justify-between p-4">
                <div>
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $item['name'] }}</h5>
                    <button class="openNoteModal"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 hover:opacity-50 active:opacity-100">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg></button>
                </div>

                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Rp. {{ number_format($item['price'], 0, ',', '.') }}</p>
                <form class="max-w-xs mx-auto">
                    <div class="relative flex items-center gap-2">
                        <button type="button" class="decrement-button" data-input-counter-decrement="quantity-input-{{ $index }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="red" class="size-8 hover:opacity-50 active:opacity-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </button>
                        <input type="text" id="quantity-input-{{ $index }}" data-input-counter aria-describedby="helper-text-explanation" class="bg-gray-50 border-gray-200 h-11 text-center text-gray-900 text-sm block w-full py-2.5 rounded-lg" value="{{$item['quantity']}}" required />
                        <button type="button" class="increment-button" data-input-counter-increment="quantity-input-{{ $index }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="blue" class="size-8 hover:opacity-50 active:opacity-100">
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
                <h3 id="total-price" class="text-lg font-bold">Rp. </h3>
            </div>
        </div>
        @else
        <p class="text-red-500">Your cart is empty. Please add items to your cart first.</p>
        @endif


        <div class="bg-[#108482] p-4 rounded-full shadow-lg border border-gray-200 mb-5 my-auto hover:opacity-70 active:opacity-100" id="openModal">
            <div class="items-center">
                <h3 class="text-lg text-white font-semibold text-center">Order Now</h3>
            </div>
        </div>

        <!-- <a href="">
                <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">debug</button>
            </a> -->

        <!-- Modal -->
        <div id="paymentModal" class="fixed inset-0 items-center justify-center hidden w-full h-full bg-black bg-opacity-50 px-6">
            <div class="bg-white rounded-lg shadow-lg p-4 w-full max-w-sm mx-auto">
                <h2 class="text-xl font-bold mb-4 text-center flex flex-col justify-center">Pilih Metode Pembayaran</h2>
                <div class="grid grid-cols-2 gap-4">
                    <button id="payCashless" class="bg-[#108482] text-white px-4 py-2 rounded hover:opacity-50 active:opacity-100">Cashless</button>
                    <button id="payCash" class="bg-[#37368E] text-white px-4 py-2 rounded hover:opacity-50 active:opacity-100">Cash</button>
                </div>
                <div class="flex justify-center">
                    <button id="closeModal" class="mt-4 text-red-500">Tutup</button>
                </div>

            </div>
        </div>


        <div id="noteModal" class="fixed inset-0 flex items-center justify-center hidden w-full h-full bg-black bg-opacity-50 px-6">
            <div class="bg-white rounded-lg shadow-lg p-4 w-full max-w-sm">
                <h2 class="text-xl font-bold mb-4 text-center">Add note to your item</h2>
                <textarea id="note" class="w-full h-32 border border-gray-200 rounded-lg p-2" placeholder="Tambahkan catatan disini"></textarea>
                <div class="flex justify-center">
                    <button id="closeNoteModal" class="mt-4 text-red-500">Tutup</button>
                </div>
            </div>
        </div>










</body>

</html>