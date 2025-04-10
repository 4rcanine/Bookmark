{{-- File: resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - LinkSaver+</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> {{-- Or use your 'cozy' font --}}

        {{-- Tagify CSS --}}
        <link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Add custom styles for transparency or specific overrides if needed --}}
        <style>
            /* Example: Style for the main content area if needed beyond Tailwind */
            .content-area {
                /* Add custom styles here if Tailwind isn't enough */
                /* E.g., backdrop-filter: blur(2px); */
            }
            /* Style for the header to potentially make it transparent or blend */
            header.main-header nav {
                /* background-color: rgba(255, 251, 235, 0.8); /* Semi-transparent cozy-cream */
                /* box-shadow: none;
                border-bottom: 1px solid rgba(139, 69, 19, 0.3); cozy-brown */
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        {{-- The body already has the background via app.css --}}
        <div class="min-h-screen flex flex-col">

            {{-- Pass a class to navigation if needed for styling --}}
            @include('layouts.navigation', ['navClass' => 'main-header'])

            @isset($header)
                {{-- Header might need different styling now - less prominent? --}}
                <header class="bg-cozy-cream/80 backdrop-blur-sm shadow-sm border-b border-cozy-brown/30 sticky top-0 z-40"> {{-- Example: Semi-transparent cream --}}
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Create the main content area overlay --}}
            <main class="flex-grow py-8 md:py-12">
                 {{-- This div centers the content and gives it the background/border --}}
                <div class="content-area max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {{-- Removed bg/shadow from here, applied below or let children handle --}}
                     {{ $slot }}
                </div>
            </main>

            {{-- Optional Footer --}}
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