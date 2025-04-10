<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LinkSaver') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased guest-layout-background">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

           
            <div class="w-full sm:max-w-md px-6 pt-24 pb-8 shadow-xl sm:rounded-lg guest-form-box relative overflow-visible">
              

                <div class="absolute top-[-50px] left-1/2 -translate-x-1/2 z-10">
                   
                    <img src="{{ asset('images/pusi.gif') }}" alt="Cute cat animation" class="w-[17.5rem] h-auto">
                </div>
              
                {{ $slot }}

            </div>
        </div>
    </body>
</html>