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
                 {{-- This div centers the content and acts as the relative parent for the TOP-RIGHT image --}}
                <div class="content-area max-w-7xl mx-auto sm:px-6 lg:px-8 relative">

                    {{-- START: Top-Right Illustration (Now GIF) --}}
                    <div class="absolute top-0 right-0 transform -translate-y-1/2 translate-x-1/4 sm:translate-x-1/3 md:translate-x-1/2 lg:right-10 z-20 pointer-events-none hidden lg:block">
                        {{-- === FILENAME UPDATED HERE === --}}
                        <img src="{{ asset('images/snorlaa.gif') }}" alt="Decorative Illustration" class="h-24 w-auto md:h-32">
                        {{-- === === === === === === === --}}
                    </div>
                    {{-- END: Top-Right Illustration --}}


                    {{-- The main page content ($slot) --}}
                    {{ $slot }}

                </div>
            </main>

            <footer class="text-center py-4 text-sm text-cozy-text/70">
                LinkSaver+ &copy; {{ date('Y') }}
            </footer>
        </div>

        {{-- START: Bottom Walking Cat Element --}}
        <img id="walking-cat-loop"
             src="{{ asset('images/walking-left-cat.gif') }}"
             alt="Walking cat animation"
             class="fixed bottom-5 left-0 z-40 w-24 h-auto pointer-events-none" {{-- Removed flip --}}
             style="left: -100px;"
             >
        {{-- END: Bottom Walking Cat Element --}}


        {{-- Tagify JS --}}
        <script src="https://unpkg.com/@yaireo/tagify"></script>
        <script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>

        {{-- Stack for page-specific scripts --}}
        @stack('scripts')

        {{-- START: Walking Cat JavaScript (Looping Right) --}}
        {{-- Script remains the same --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const cat = document.getElementById('walking-cat-loop');
                if (!cat) return;

                const speed = 0.75; // Slow speed
                let catWidth = cat.offsetWidth;
                if (catWidth === 0) catWidth = 96; // Estimate for w-24

                let position = -catWidth; // Start position off-screen left

                cat.style.left = `${position}px`; // Set initial position

                function animateCatLoop() {
                    catWidth = cat.offsetWidth;
                    if (catWidth === 0) catWidth = 96;

                    position += speed; // Move right

                    if (position > window.innerWidth) { // Check if off-screen right
                        position = -catWidth; // Reset to off-screen left
                    }

                    cat.style.left = `${position}px`; // Update position

                    requestAnimationFrame(animateCatLoop); // Loop
                }

                setTimeout(() => {
                    requestAnimationFrame(animateCatLoop);
                }, 100);

                 window.addEventListener('resize', () => {
                    if (parseFloat(cat.style.left) > window.innerWidth) {
                         position = -catWidth;
                         cat.style.left = `${position}px`;
                    }
                 });
            });
        </script>
        {{-- END: Walking Cat JavaScript --}}

    </body>
</html>