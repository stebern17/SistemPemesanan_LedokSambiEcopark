<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ledok Sambi Ecopark</title>
    <script>
        window.csrf_token = "{{ csrf_token() }}"; // Add this line
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <header>
        <div class="relative h-[45vh]">
            <div class="absolute bg-[#108482] rounded-b-3xl h-40 w-full"></div>
            <div class="relative">
                <img src="{{ asset('images/Logo Ledok Sambi.png') }}" alt="logo" class="h-20 object-cover mx-auto py-2" />
            </div>
            <div class="mx-auto w-11/12">
                <div id="default-carousel" class="relative w-full" data-carousel="slide">
                    <div class="relative h-56 overflow-hidden rounded-2xl">
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                            <img src="{{ asset('images/ledoksambi1.png') }}" class="w-full h-full object-cover" alt="picture">
                        </div>
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                            <img src="{{ asset('images/ledoksambi2.jpg') }}" class="w-full h-full object-cover" alt="...">
                        </div>
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                            <img src="{{ asset('images/ledoksambi3.jpeg') }}" class="w-full h-full object-cover" alt="...">
                        </div>
                    </div>
                    <button type="button" class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-white sm:w-6 sm:h-6 dark:text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="hidden sm:inline-block">Previous</span>
                        </span>
                    </button>
                    <button type="button" class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-white sm:w-6 sm:h-6 dark:text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <span class="hidden sm:inline-block">Next</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div>
        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
            <ul class="flex flex-wrap justify-center gap-4 md:gap-10">
                <li class="me-2">
                    <a href="{{ route('welcome', ['category' => 'food']) }}" class="inline-block p-4 border-b-2 {{ $category == 'food' ? 'text-[#108482] border-[#108482]' : 'border-transparent' }} rounded-t-lg hover:text-gray-600 active hover:border-gray-300 dark:hover:text-gray-300">Food</a>
                </li>
                <li class="me-2">
                    <a href="{{ route('welcome', ['category' => 'drink']) }}" class="inline-block p-4 border-b-2 {{ $category == 'drink' ? 'text-[#108482] border-[#108482]' : 'border-transparent' }} rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">Drink</a>
                </li>
                <li class="me-2">
                    <a href="{{ route('welcome', ['category' => 'sidedish']) }}" class="inline-block p-4 border-b-2 {{ $category == 'sidedish' ? 'text-[#108482] border-[#108482]' : 'border-transparent' }} rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">Sidedish</a>
                </li>
            </ul>
        </div>
        <div>
            <div class="px-2 mt-2 flex gap-2 text-center">
                <h3 class="font-semibold p-4">You're in table :</h3>
                <p class="bg-[#108482] text-white py-4 px-5 rounded-lg drop-shadow-lg">{{($cartData['tableNumber'])}}</p>
            </div>
        </div>
        <div id="itemsCard" class="container grid grid-cols-2 gap-4 p-4">
            @foreach($menus as $menu)
            <div class="max-w-sm max-h-sm bg-white border flex flex-col justify-between border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <img class="rounded-t-lg w-full max-h-32 object-cover" src="{{ asset('storage/'. $menu?->image) }}" alt="{{ ucfirst($menu->name) }} Image" />
                <div class="p-5">
                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ucfirst($menu->name)}}</h5>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Rp. {{ number_format($menu->price, 0, ',', '.') }}</p>
                    <a href="{{route('add-to-cart')}}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-[#108482] rounded-lg hover:opacity-50 add-to-cart" data-name="{{ucfirst($menu->name)}}" data-price="{{$menu->price}}">
                        Add
                        <svg class="w-6 h-6 ms-2 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10V6a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4m3-2 .917 11.923A1 1 0 0 1 17.92 21H6.08a1 1 0 0 1-.997-1.077L6 8h12Z" />
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div id="cartButton" class="fixed bottom-0 left-1/2 transform -translate-x-1/2 mb-4 w-[80vw] items-center hidden">
            <a href="{{ route('checkout') }}" class="bg-[#108482] text-white p-2 rounded-full shadow-lg text-center flex justify-between px-6 py-3 hover:opacity-90">
                <p id="itemsCount">0 Items</p>
                <div class="flex">
                    <p id="itemsPrice">Rp. 0</p>
                    <svg class="w-6 h-6 ms-2 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10V6a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4m3-2 .917 11.923A1 1 0 0 1 17.92 21H6.08a1 1 0 0 1-.997-1.077L6 8h12Z" />
                    </svg>
                </div>
            </a>
        </div>

    </div>
</body>

</html>