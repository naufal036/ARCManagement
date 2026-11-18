@php
    $navigationGroups = [
        [
            'label' => 'Utama',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'dashboard',
                    'active' => 'dashboard',
                    'icon' => 'M3 3h7v7H3z M14 3h7v7h-7z M14 14h7v7h-7z M3 14h7v7H3z',
                ],
                [
                    'label' => 'Proyek',
                    'route' => 'proyek.index',
                    'active' => 'proyek.*',
                    'icon' => 'M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z',
                ],
                [
                    'label' => 'Kontrak',
                    'route' => 'contract.index',
                    'active' => 'contract.*',
                    'icon' =>
                        'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z',
                ],
                [
                    'label' => 'Penugasan',
                    'route' => 'project-assignment.index',
                    'active' => 'project-assignment.*',
                    'icon' =>
                        'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4',
                ],
                [
                    'label' => 'Dokumen',
                    'icon' => 'M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z M14 3v5h5 M9 13h6 M9 17h6',
                ],
                [
                    'label' => 'Progres',
                    'route' => 'progress-proyek.index',
                    'active' => 'progress-proyek.*',
                    'icon' => 'M3 3v18h18 M7 14l4-4 4 3 6-7',
                ],
                ['label' => 'Laporan', 'icon' => 'M4 19V5 M4 19h17 M8 16v-5 M13 16V7 M18 16V9'],
                [
                    'label' => 'Klien',
                    'route' => 'klien.index',
                    'active' => 'klien.*',
                    'icon' =>
                        'M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M20 8v6 M23 11h-6',
                ],
                [
                    'label' => 'Konsultasi',
                    'icon' =>
                        'M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8z',
                ],
                ['label' => 'Jadwal', 'icon' => 'M8 2v4 M16 2v4 M3 10h18 M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2z'],
            ],
        ],
        [
            'label' => 'Manajemen',
            'items' => [
                ['label' => 'Anggaran', 'icon' => 'M3 6h18v13H3z M3 10h18 M7 15h3'],
                [
                    'label' => 'Tim',
                    'icon' =>
                        'M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M20 8v6 M23 11h-6',
                ],
            ],
        ],
        [
            'label' => 'Data Master',
            'items' => [
                [
                    'label' => 'Klien',
                    'route' => 'klien.index',
                    'active' => 'klien.*',
                    'icon' =>
                        'M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M20 8v6 M23 11h-6',
                ],
                [
                    'label' => 'Jenis Pekerjaan',
                    'route' => 'jenis-pekerjaan.index',
                    'active' => 'jenis-pekerjaan.*',
                    'icon' => 'M4 4h16v16H4z M8 8h8 M8 12h8 M8 16h5',
                ],
            ],
        ],
        [
            'label' => 'Operasional Proyek',
            'items' => [
                [
                    'label' => 'Proyek',
                    'route' => 'proyek.index',
                    'active' => 'proyek.*',
                    'icon' => 'M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z',
                ],
                [
                    'label' => 'Kontrak',
                    'route' => 'contract.index',
                    'active' => 'contract.*',
                    'icon' => 'M6 3h9l3 3v15H6z M15 3v4h4 M9 12h6 M9 16h6',
                ],
                [
                    'label' => 'Project Assignment',
                    'route' => 'project-assignment.index',
                    'active' => 'project-assignment.*',
                    'icon' =>
                        'M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M17 11h4 M19 9v4',
                ],
                [
                    'label' => 'Progress Reporting',
                    'route' => 'progress-proyek.index',
                    'active' => 'progress-proyek.*',
                    'icon' => 'M3 3v18h18 M7 14l4-4 4 3 6-7',
                ],
            ],
        ],
        [
            'label' => 'Document Management',
            'items' => [
                [
                    'label' => 'Daftar Dokumen',
                    'route' => 'dokumen-proyek.index',
                    'active' => 'dokumen-proyek.*',
                    'icon' => 'M6 3h8l4 4v14H6z M14 3v5h5 M9 13h6 M9 17h6',
                ],
                [
                    'label' => 'Laporan Dokumentasi',
                    'route' => 'laporan-dokumentasi.index',
                    'active' => 'laporan-dokumentasi.*',
                    'icon' => 'M4 4h16v16H4z M8 8h8 M8 12h5 M8 16h8',
                ],
            ],
        ],
    ];

    // Menambahkan Kategori Dokumen ke dalam grup Document Management (untuk Admin & Manajer)
    if (
        Auth::user()?->role &&
        in_array(Auth::user()->role->name, ['Admin', 'Manajer'], true) &&
        \Illuminate\Support\Facades\Route::has('kategori-dokumen.index')
    ) {
        $navigationGroups[array_key_last($navigationGroups)]['items'][] = [
            'label' => 'Kategori Dokumen',
            'route' => 'kategori-dokumen.index',
            'active' => 'kategori-dokumen.*',
            'icon' => 'M4 6h16M4 12h16M4 18h10',
        ];
    }

    // Menu khusus Admin (cukup 1 blok)
    if (Auth::user()?->role && Auth::user()->role->name === 'Admin') {
        $navigationGroups[] = [
            'label' => 'Administrasi',
            'items' => [
                [
                    'label' => 'Manajemen Pengguna',
                    'route' => 'users.index',
                    'active' => 'users.*',
                    'icon' =>
                        'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2 M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M23 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75',
                ],
                [
                    'label' => 'Pengaturan Sistem',
                    'route' => 'settings.index',
                    'active' => 'settings.*',
                    'icon' =>
                        'M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8z M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-1.6 2.8-.2-.1a1.7 1.7 0 0 0-1.8.2l-.2.1h-3.2l-.1-.2a1.7 1.7 0 0 0-1.5-1l-.2-.1-1.6-2.8.1-.2a1.7 1.7 0 0 0-.3-1.8l-.1-.2v-3.2l.2-.1a1.7 1.7 0 0 0 1-1.5l.1-.2 2.8-1.6.2.1a1.7 1.7 0 0 0 1.8-.3l.2-.1h3.2l.1.2a1.7 1.7 0 0 0 1.5 1l.2.1 1.6 2.8-.1.2a1.7 1.7 0 0 0 .3 1.8l.1.2v3.2l-.2.1a1.7 1.7 0 0 0-1 1.5z',
                ],
            ],
        ];
    }
@endphp

<div x-show="mobileNavOpen" x-cloak @click="mobileNavOpen = false" class="fixed inset-0 z-40 bg-[#071A33]/60 lg:hidden"
    aria-hidden="true"></div>

<aside id="app-sidebar"
    class="app-sidebar fixed inset-y-0 start-0 z-50 flex h-screen flex-col border-e border-white/10 bg-[#0A2342] text-white shadow-xl transition-[width,transform] duration-200 ease-out"
    :class="(mobileNavOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0') + (sidebarCollapsed ?
        ' app-sidebar--collapsed' : '')"
    aria-label="Navigasi utama">
    <div class="flex h-[76px] shrink-0 items-center justify-between border-b border-white/10 px-5">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3" aria-label="ARCManagement Dashboard">
            <span
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#14B8A6] text-sm font-bold tracking-wide text-[#0A2342]">ARC</span>
            <span x-show="!sidebarCollapsed" x-cloak class="min-w-0">
                <span class="block truncate text-sm font-semibold tracking-wide">
                    {{ \App\Models\SystemConfig::where('config_key', 'app_name')->value('config_value') ?? 'ARC Management' }}
                </span>
                <span class="mt-0.5 block text-[11px] text-slate-300">Konsultasi Konstruksi</span>
            </span>
        </a>
        <button type="button" @click="mobileNavOpen = false"
            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-300 hover:bg-white/10 hover:text-white lg:hidden"
            aria-label="Tutup navigasi">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round">
                <path d="m18 6-12 12M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="min-h-0 flex-1 space-y-7 overflow-y-auto px-3 py-6">
        @foreach ($navigationGroups as $group)
            <section aria-label="{{ $group['label'] }}">
                <p x-show="!sidebarCollapsed" x-cloak
                    class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">
                    {{ $group['label'] }}</p>
                <div class="space-y-1.5">
                    @foreach ($group['items'] as $item)
                        @php
                            $hasRoute = isset($item['route']) && \Illuminate\Support\Facades\Route::has($item['route']);
                            $isActive = $hasRoute && request()->routeIs($item['active']);
                        @endphp
                        <a href="{{ $hasRoute ? route($item['route']) : '#' }}"
                            @if ($isActive) aria-current="page" @endif
                            :title="sidebarCollapsed ? '{{ $item['label'] }}' : null"
                            class="flex min-h-11 items-center gap-3 rounded-xl border-s-2 px-3 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#14B8A6] {{ $isActive ? 'border-[#14B8A6] bg-white/10 text-white' : 'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' }}"
                            :class="sidebarCollapsed ? 'justify-center !px-0' : 'justify-start'">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="{{ $item['icon'] }}" />
                            </svg>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </nav>

    <div class="shrink-0 border-t border-white/10 p-3">
        <div class="flex items-center gap-3 rounded-lg px-2 py-2" :class="sidebarCollapsed ? 'justify-center' : ''">
            @if (Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Foto Profil"
                    class="h-9 w-9 shrink-0 rounded-full object-cover">
            @else
                <span
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#2A6F97] text-sm font-semibold text-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </span>
            @endif
            <div x-show="!sidebarCollapsed" x-cloak class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit"
                class="flex min-h-10 w-full items-center gap-3 rounded-xl px-3 text-sm font-medium text-slate-300 transition hover:bg-white/5 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#14B8A6]"
                :class="sidebarCollapsed ? 'justify-center !px-0' : 'justify-start'">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 17l5-5-5-5M15 12H3M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6" />
                </svg>
                <span x-show="!sidebarCollapsed" x-cloak>{{ __('Keluar') }}</span>
            </button>
        </form>
    </div>
</aside>

<header
    class="app-topbar sticky top-0 z-30 flex h-[76px] items-center justify-between gap-4 border-b border-[#E3E8EF] bg-white px-4 sm:px-6 lg:px-8"
    :class="sidebarCollapsed ? 'app-topbar--collapsed' : ''">
    <div class="flex min-w-0 flex-1 items-center gap-3">
        <button type="button" @click="sidebarCollapsed = !sidebarCollapsed"
            class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-xl text-[#5B6B80] transition hover:bg-[#F5F8FA] hover:text-[#0A2342] focus:outline-none focus:ring-2 focus:ring-[#14B8A6] lg:inline-flex"
            :aria-label="sidebarCollapsed ? 'Perluas sidebar' : 'Ciutkan sidebar'" :aria-expanded="!sidebarCollapsed">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round">
                <path d="M4 5h16M4 12h16M4 19h16M9 5v14" />
            </svg>
        </button>
        <button type="button" @click="mobileNavOpen = true"
            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-[#E3E8EF] text-[#0A2342] lg:hidden"
            aria-label="Buka navigasi" aria-controls="app-sidebar">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round">
                <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <a href="{{ route('dashboard') }}" class="hidden items-center gap-2 lg:flex"
            aria-label="ARCManagement Dashboard">
            <span
                class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#0A2342] text-xs font-bold text-white">ARC</span>
            <span class="text-sm font-bold tracking-wide text-[#0A2342]">ARCManagement</span>
        </a>
        <span class="hidden h-6 w-px bg-[#E3E8EF] lg:block"></span>
        <p class="hidden text-sm text-[#5B6B80] lg:block">Manajemen Konstruksi Terpadu</p>
    </div>

    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
        <button type="button" disabled title="Notifikasi belum tersedia" aria-label="Notifikasi belum tersedia"
            class="relative inline-flex h-10 w-10 cursor-not-allowed items-center justify-center rounded-xl text-[#5B6B80] opacity-70">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
            </svg>
        </button>
        <x-dropdown align="right" width="48" contentClasses="py-1 bg-white ring-1 ring-[#E3E8EF] shadow-xl">
            <x-slot name="trigger">
                <button type="button"
                    class="flex min-h-11 items-center gap-2 rounded-xl px-2 text-start transition hover:bg-[#F5F8FA] focus:outline-none focus:ring-2 focus:ring-[#14B8A6] sm:gap-3 sm:px-3"
                    aria-label="Buka menu profil">
                    @if (Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Foto Profil"
                            class="h-9 w-9 shrink-0 rounded-full object-cover border border-[#E3E8EF]">
                    @else
                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#E6F4F3] text-sm font-semibold text-[#0A2342]">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </span>
                    @endif
                    <span class="hidden min-w-0 text-start sm:block">
                        <span
                            class="block max-w-36 truncate text-sm font-semibold text-[#0F1B2D]">{{ Auth::user()->name }}</span>
                        <span class="block text-xs text-[#5B6B80]">{{ Auth::user()->role->name ?? 'Pengguna' }}</span>
                    </span>
                    <svg class="hidden h-4 w-4 text-[#5B6B80] sm:block" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <path d="m7 10 5 5 5-5" />
                    </svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profil') }}
                </x-dropdown-link>
                <x-dropdown-link :href="route('profile.edit') . '#update-password'">
                    {{ __('Ganti Password') }}
                </x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Keluar') }}</x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>

<nav class="fixed inset-x-0 bottom-0 z-30 grid grid-cols-4 border-t border-[#E3E8EF] bg-white/95 pb-[env(safe-area-inset-bottom)] shadow-[0_-8px_24px_rgba(15,27,45,0.06)] backdrop-blur lg:hidden"
    aria-label="Navigasi mobile">
    @foreach ([['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard', 'icon' => 'M3 3h7v7H3z M14 3h7v7h-7z M14 14h7v7h-7z M3 14h7v7H3z'], ['label' => 'Proyek', 'route' => 'proyek.index', 'active' => 'proyek.*', 'icon' => 'M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'], ['label' => 'Klien', 'route' => 'klien.index', 'active' => 'klien.*', 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M10 11a4 4 0 1 0-8 0 4 4 0 0 0 0 8 M20 8v6 M23 11h-6'], ['label' => 'Progress', 'route' => 'progress-proyek.index', 'active' => 'progress-proyek.*', 'icon' => 'M3 3v18h18 M7 14l4-4 4 3 6-7']] as $item)
        @php $isActive = request()->routeIs($item['active']); @endphp
        <a href="{{ route($item['route']) }}" @if ($isActive) aria-current="page" @endif
            class="flex min-h-16 flex-col items-center justify-center gap-1 px-1 text-[10px] font-medium {{ $isActive ? 'text-[#0A2342]' : 'text-[#5B6B80]' }}">
            <svg class="h-5 w-5 {{ $isActive ? 'text-[#14B8A6]' : '' }}" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $item['icon'] }}" />
            </svg>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>
