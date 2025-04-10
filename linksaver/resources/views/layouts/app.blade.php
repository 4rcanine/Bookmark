{{-- File: resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - LinkSaver+</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- Tagify CSS --}}
        <link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Optional custom styles */
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col">

            @include('layouts.navigation', ['navClass' => 'main-header'])

            @isset($header)
                <header class="bg-cozy-cream/80 backdrop-blur-sm shadow-sm border-b border-cozy-brown/30 sticky top-0 z-40">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-grow py-8 md:py-12">
                 {{-- This div centers the content and acts as the relative parent for the cat --}}
                <div class="content-area max-w-7xl mx-auto sm:px-6 lg:px-8 relative"> {{-- <-- Added relative positioning --}}

                    {{-- START: Add the Cat Illustration --}}
                    <div class="absolute top-0 right-0 transform -translate-y-1/2 translate-x-1/4 sm:translate-x-1/3 md:translate-x-1/2 lg:right-10 z-20 pointer-events-none hidden lg:block">
                        {{--
                            - absolute: Positions relative to the nearest positioned ancestor (the content-area div).
                            - top-0: Aligns the top of the image container with the top of the content-area before translation.
                            - right-0 / lg:right-10: Aligns to the right edge (adjust lg:right-10 as needed for spacing).
                            - transform: Enables transforms.
                            - -translate-y-1/2: Pulls the image container up by half its height, centering it vertically on the top edge.
                            - translate-x-1/4 etc: Pushes the image slightly right (adjust as needed).
                            - z-20: Ensures it's above the content area background/border.
                            - pointer-events-none: Prevents the image from blocking clicks on elements behind it.
                            - hidden lg:block: Hides the cat on smaller screens (adjust breakpoint if needed).
                        --}}
                        <img src="{{ asset('images/cat-illustration.png') }}" alt="Decorative Cat Illustration" class="h-24 w-auto md:h-32"> {{-- Adjust height (h-24/h-32) as needed --}}
                    </div>
                    {{-- END: Add the Cat Illustration --}}


                    {{-- The main page content ($slot) --}}
                    {{ $slot }}

                </div>
            </main>

            <footer class="text-center py-4 text-sm text-cozy-text/70">
                LinkSaver+ &copy; {{ date('Y') }}
            </footer>
        </div>

        {{-- Tagify JS --}}
        <script src="https://unpkg.com/@yaireo/tagify"></script>
        <script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>

        {{-- Stack for page-specific scripts --}}
        @stack('scripts')

    </body>
</html>