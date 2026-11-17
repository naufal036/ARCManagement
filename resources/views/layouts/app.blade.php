<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ARCManagement') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo1.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-inter antialiased">
    <div
        x-data="{ sidebarCollapsed: false, mobileNavOpen: false }"
        @resize.window.debounce.100ms="if (window.innerWidth < 1024) sidebarCollapsed = false"
        @keydown.escape.window="mobileNavOpen = false"
        class="min-h-screen bg-[#F5F8FA] text-[#0F1B2D]"
    >
        @include('layouts.navigation')

        <div class="app-main" :class="sidebarCollapsed ? 'app-main--collapsed' : ''">
            @if (isset($header))
                <header class="border-b border-[#E3E8EF] bg-white">
                    <div class="px-4 py-5 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @elseif (View::hasSection('header'))
                <header class="border-b border-[#E3E8EF] bg-white">
                    <div class="px-4 py-5 sm:px-6 lg:px-8">
                        <h2 class="text-xl font-semibold leading-tight text-[#0F1B2D]">
                            @yield('header')
                        </h2>
                    </div>
                </header>
            @endif

            <main class="p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
