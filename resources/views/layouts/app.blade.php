<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-inter antialiased">
        <div
            x-data="{ sidebarCollapsed: false, mobileNavOpen: false }"
            @resize.window.debounce.100ms="if (window.innerWidth < 1024) sidebarCollapsed = false"
            @keydown.escape.window="mobileNavOpen = false"
            class="min-h-screen bg-[#F4F7FB] text-[#1E293B]"
        >
            @include('layouts.navigation')

            <div class="app-main" :class="sidebarCollapsed ? 'app-main--collapsed' : ''">
                @isset($header)
                    <header class="border-b border-[#E2E8F0] bg-white">
                        <div class="px-4 py-5 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="pb-20 lg:pb-0">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
