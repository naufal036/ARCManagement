<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ARCManagement') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('images/logo1.png') }}">
</head>

<body class="bg-gray-100 min-h-screen flex flex-col font-sans text-gray-900">

    <!-- Top Navbar Full-Width -->
    <header class="bg-slate-900 text-white shadow-md px-6 py-3 flex items-center justify-between z-30">
        <!-- Kiri: Logo & Nama Web -->
        <div class="flex-1 flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-9 h-9 object-contain">
            <h1 class="text-xl font-bold tracking-wider">
                {{ \App\Models\SystemConfig::where('config_key', 'app_name')->value('config_value') ?? 'ARC Management' }}
            </h1>
        </div>

        <!-- Tengah: Jam & Tanggal Real-time -->
        <div class="text-center px-4">
            <p id="navbar-clock" class="text-xs text-slate-300 font-medium">--:--:-- WIB</p>
            <p id="navbar-date" class="text-sm font-bold text-white">Memuat tanggal...</p>
        </div>

        <!-- Kanan: Kapsul Profil & Dropdown -->
        <div class="flex-1 flex justify-end">
            <div class="relative" id="profileDropdownContainer">
                <button type="button" onclick="toggleProfileDropdown()"
                    class="flex items-center gap-3 bg-white hover:bg-gray-100 border border-gray-200 rounded-full pl-2 pr-4 py-1.5 transition focus:outline-none shadow-sm">

                    <!-- Foto Profil atau Inisial Nama -->
                    @if (Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Foto Profil"
                            class="w-9 h-9 rounded-full object-cover border border-gray-300 bg-white">
                    @else
                        <div
                            class="w-9 h-9 rounded-full bg-slate-200 text-slate-800 font-bold text-sm flex items-center justify-center border border-gray-300">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                        </div>
                    @endif

                    <!-- Baris Atas: Nama Pengguna | Baris Bawah: Role -->
                    <div class="text-left leading-tight">
                        <p class="text-sm font-bold text-gray-800 max-w-[160px] truncate">
                            {{ Auth::user()->name ?? 'User' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ Auth::user()->role->name ?? 'Pengguna' }}
                        </p>
                    </div>
                </button>

                <!-- Dropdown Menu -->
                <div id="profileDropdownMenu"
                    class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50 text-gray-700">

                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span>Profil</span>
                    </a>

                    <a href="{{ route('profile.edit') }}#update-password"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <span>Ganti Password</span>
                    </a>

                    <div class="border-t border-gray-100 my-1"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition text-left">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-red-500">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Wrapper Sidebar & Konten -->
    <div class="flex flex-1">
        <!-- Sidebar Komponen -->
        <aside class="w-64 bg-slate-800 text-white flex flex-col">
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('dashboard') }}"
                    class="block p-3 rounded transition {{ request()->routeIs('dashboard') ? 'bg-slate-700 font-semibold' : 'hover:bg-slate-700' }}">Dashboard</a>

                @if (Auth::user()->role && Auth::user()->role->name === 'Admin')
                    <a href="{{ route('users.index') }}"
                        class="block p-3 rounded transition {{ request()->routeIs('users.*') ? 'bg-slate-700 font-semibold' : 'hover:bg-slate-700' }}">Manajemen
                        Pengguna</a>

                    <a href="{{ route('settings.index') }}"
                        class="block p-3 rounded transition {{ request()->routeIs('settings.*') ? 'bg-slate-700 font-semibold' : 'hover:bg-slate-700' }}">Pengaturan
                        Sistem</a>
                @endif
            </nav>
        </aside>

        <!-- Area Konten Dinamis -->
        <main class="p-6 flex-1 overflow-y-auto">
            @hasSection('header')
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">@yield('header')</h2>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleProfileDropdown() {
            document.getElementById('profileDropdownMenu').classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            const container = document.getElementById('profileDropdownContainer');
            const menu = document.getElementById('profileDropdownMenu');
            if (container && !container.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        function updateNavbarDateTime() {
            const now = new Date();
            const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            const namaHari = hari[now.getDay()];
            const tanggal = String(now.getDate()).padStart(2, '0');
            const namaBulan = bulan[now.getMonth()];
            const tahun = now.getFullYear();

            const jam = String(now.getHours()).padStart(2, '0');
            const menit = String(now.getMinutes()).padStart(2, '0');
            const detik = String(now.getSeconds()).padStart(2, '0');

            document.getElementById('navbar-clock').textContent = `${jam}:${menit}:${detik} WIB`;
            document.getElementById('navbar-date').textContent = `${namaHari}, ${tanggal} ${namaBulan} ${tahun}`;
        }

        updateNavbarDateTime();
        setInterval(updateNavbarDateTime, 1000);
    </script>

</body>

</html>
