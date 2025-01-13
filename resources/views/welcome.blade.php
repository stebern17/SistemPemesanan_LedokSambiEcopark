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
        <div class="relative h-[42vh] ">
            <div class="items-center bg-[#108482] rounded-b-3xl h-40">
                <img src="{{ asset('images/Logo Ledok Sambi.png') }}" alt="logo" class="h-20 object-cover mx-auto py-2" />
            </div>
            <div class="absolute inset-x-0 bottom-6 mx-auto w-11/12">
                <div class="bg-white rounded-3xl p-2 shadow-lg">
                    <div class="relative">
                        <button class="absolute left-2 top-1/2 -translate-y-1/2 bg-white rounded-full p-2 shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <div>
                            <img src="{{ asset('images/ledoksambi1.jpeg') }}" alt="image" class="w-full h-30 object-cover rounded-2xl" />
                        </div>
                        <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-white rounded-full p-2 shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
</body>

</html>