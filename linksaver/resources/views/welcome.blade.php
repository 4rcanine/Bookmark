<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>LinkSaver</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])


</head>
<body class="linksaver-bg-image font-sans antialiased">

    <div class="background-overlay"></div>

    <div class="content-wrapper">

       

        <main class="flex flex-col items-center justify-center flex-grow px-6 text-center">
           <div class="max-w-2xl">
                 <svg class="mx-auto h-16 w-auto mb-6 text-white/90 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" /> </svg>
                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl dark:text-white"> Welcome to LinkSaver </h1>
                <p class="mt-4 text-lg leading-8 text-gray-200 dark:text-gray-300"> Your simple, elegant solution to organize and access all your important links from anywhere. Never lose a link again. </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                     @if (Route::has('register'))
                         <a href="{{ route('register') }}" class="rounded-md linksaver-button px-5 py-3 text-base shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"> Get Started Free </a>
                     @endif
                     @guest
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="text-base font-semibold leading-6 text-white hover:text-white/80 dark:text-white dark:hover:text-gray-300"> Log in <span aria-hidden="true">→</span> </a>
                         @endif
                    @endguest
                </div>
            </div>
        </main>

        <footer class="py-6 text-center text-sm text-gray-300 dark:text-gray-400 w-full max-w-6xl px-6">
           Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }}) <br> © {{ date('Y') }} LinkSaver. All rights reserved.
        </footer>

    </div> 

</body>
</html>