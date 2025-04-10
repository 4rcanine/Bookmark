<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LinkSaver') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts and Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased guest-layout-background">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

            {{-- The styled box containing the form and the cat --}}
            {{-- Added: relative, overflow-visible (or just remove overflow-hidden) --}}
            {{-- Adjusted: Increased pt significantly (e.g., pt-24), removed mt-6 --}}
            <div class="w-full sm:max-w-md px-6 pt-24 pb-8 shadow-xl sm:rounded-lg guest-form-box relative overflow-visible">
                {{-- Note: Increased pt-24 significantly for cat space --}}
                {{-- Note: Removed overflow-hidden, added relative --}}

                {{-- ***** CAT IMAGE INSIDE & ABSOLUTELY POSITIONED ***** --}}
                <div class="absolute top-[-50px] left-1/2 -translate-x-1/2 z-10">
                    {{-- Adjust top-[-50px] to control overlap amount --}}
                    {{-- left-1/2 -translate-x-1/2 centers it horizontally --}}
                    {{-- Make sure pusi.gif is in public/css/images/ --}}
                    {{-- Set width using arbitrary value w-[17.5rem] (approx 70 * 4px) --}}
                    <img src="{{ asset('images/pusi.gif') }}" alt="Cute cat animation" class="w-[17.5rem] h-auto">
                </div>
                {{-- ***** END CAT IMAGE SECTION ***** --}}


                {{-- This renders the login/register form fields below the cat space --}}
                {{ $slot }}

            </div>
        </div>
    </body>
</html>