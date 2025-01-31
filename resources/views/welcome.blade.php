<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ledok Sambi Ecopark</title>
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
                            <img src="{{ asset('images/ledoksambi1.png') }}" class="w-full h-full object-cover" alt="...">
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
        <div class="mt-2 grid grid-cols-2 gap-4 p-4">
            @foreach($menus as $menu)
            <div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">

                <img class="rounded-t-lg" src="https://dummyimage.com/600x400/000/fff" alt="Menus Image" />

                <div class="p-5">
                    <a href="#">
                        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ucfirst($menu->name)}}</h5>
                    </a>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Rp. {{ number_format($menu->price, 0, ',', '.') }}</p>
                    <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-[#108482] rounded-lg hover:opacity-50 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Add
                        <svg class="w-6 h-6 ms-2 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10V6a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4m3-2 .917 11.923A1 1 0 0 1 17.92 21H6.08a1 1 0 0 1-.997-1.077L6 8h12Z" />
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>



    </div>
</body>

</html>