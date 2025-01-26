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
        <div class="relative h-[42vh] w-full">
            <div class="items-center bg-[#108482] rounded-b-3xl h-40">
                <img src="{{ asset('images/Logo Ledok Sambi.png') }}" alt="logo" class="h-20 object-cover mx-auto py-2" />
            </div>
            <div class="absolute inset-x-0 bottom-6 mx-auto w-11/12">
                <div id="default-carousel" class="relative w-full" data-carousel="slide">
                    <div class="relative h-56 overflow-hidden rounded-2xl md:h-96">
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                            <img src="{{ asset('images/ledoksambi1.png') }}" class="absolute block w-full h-full object-cover top-0 left-0" alt="...">
                        </div>
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                            <img src="{{ asset('images/ledoksambi2.jpg') }}" class="absolute block w-full h-full object-cover top-0 left-0" alt="...">
                        </div>
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                            <img src="{{ asset('images/ledoksambi3.jpeg') }}" class="absolute block w-full h-full object-cover top-0 left-0" alt="...">
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
                    <a href="#" class="inline-block p-4 border-b-2 text-[#108482] border-[#108482] rounded-t-lg hover:text-gray-600 active hover:border-gray-300 dark:hover:text-gray-300" aria-current="page">Food</a>
                </li>
                <li class="me-2">
                    <a href="#" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">Drink</a>
                </li>
                <li class="me-2">
                    <a href="#" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">Sidedish</a>
                </li>
            </ul>
        </div>
    </div>
</body>

</html>