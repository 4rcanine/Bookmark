{{-- File: resources/views/layouts/app.blade.php --}}
{{-- This file defines the overall page structure --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900"> {{-- Adjusted for dark mode consistency --}}

            {{-- ========================================================== --}}
            {{-- THIS IS THE LINE THAT INCLUDES THE NAVIGATION BAR         --}}
            {{-- You need to edit the 'layouts.navigation' file           --}}
            {{-- (resources/views/layouts/navigation.blade.php)           --}}
            {{-- to add the "Bookmarks" and "Categories" links            --}}
            {{-- ========================================================== --}}
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow"> {{-- Adjusted for dark mode --}}
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }} {{-- This is where your page-specific content (like bookmark list) goes --}}
            </main>
        </div>
        <script src="https://unpkg.com/@yaireo/tagify"></script>
        <script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script> {{-- Recommended polyfills --}}
        {{-- END: Add Tagify JS --}}

        {{-- Stack for page-specific scripts (like Tagify initialization) --}}
        @stack('scripts')

    </body>
</html>