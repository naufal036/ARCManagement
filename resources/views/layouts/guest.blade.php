<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            try {
                $appName = \App\Models\SystemConfig::where('config_key', 'app_name')->value('config_value') ?: config('app.name', 'ARC Management');
            } catch (\Throwable $e) {
                $appName = config('app.name', 'ARC Management');
            }
        @endphp

        <title>{{ $appName }} - Masuk</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('images/logo1.png')))
            <link rel="icon" type="image/png" href="{{ asset('images/logo1.png') }}">
        @endif

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#0F1B2D] antialiased bg-white">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

            <!-- Panel Kiri: Logo, Form Login, dan Footer -->
            <div class="flex flex-col justify-between px-6 py-8 sm:px-12 lg:px-16 bg-white">
                <!-- Brand Header Pojok Kiri Atas -->
                <div>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-3 group">
                        @if (file_exists(public_path('images/logo1.png')))
                            <img src="{{ asset('images/logo1.png') }}" alt="Logo {{ $appName }}" class="w-10 h-10 object-contain rounded-xl">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-[#14B8A6] flex items-center justify-center text-white font-bold text-sm tracking-wide shadow-sm">
                                ARC
                            </div>
                        @endif
                        <div>
                            <span class="block text-lg font-bold text-[#0A2342] leading-tight group-hover:text-[#14B8A6] transition">
                                {{ $appName }}
                            </span>
                            <span class="block text-xs text-gray-500">Konsultasi Konstruksi</span>
                        </div>
                    </a>
                </div>

                <!-- Konten Utama Form (Tengah) -->
                <div class="w-full max-w-md mx-auto my-10">
                    {{ $slot }}
                </div>

                <!-- Footer Bawah -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-400 border-t border-gray-100 pt-4">
                    <span>Copyright &copy; {{ date('Y') }} {{ $appName }}.</span>
                    <span>Sistem Manajemen Proyek &amp; Konstruksi</span>
                </div>
            </div>

            <!-- Panel Kanan: Showcase Visual & Pratinjau Dashboard -->
            <div class="hidden lg:flex p-5 bg-white">
                <div class="relative w-full rounded-3xl bg-gradient-to-br from-[#0A2342] via-[#103663] to-[#0D9488] p-10 xl:p-14 flex flex-col justify-between overflow-hidden text-white shadow-xl">
                    <!-- Aksen Dekoratif Latar -->
                    <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
                    <div class="absolute -bottom-28 -left-20 w-96 h-96 rounded-full bg-[#14B8A6]/15 blur-3xl pointer-events-none"></div>

                    <!-- Teks Utama -->
                    <div class="relative z-10 max-w-lg mt-2">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-xs font-medium text-teal-200 mb-5">
                            <span class="w-2 h-2 rounded-full bg-[#14B8A6]"></span>
                            Portal Internal Terintegrasi
                        </span>
                        <h2 class="text-3xl xl:text-4xl font-bold leading-tight tracking-tight">
                            Kelola tim dan operasional proyek dengan mudah.
                        </h2>
                        <p class="mt-3 text-sm xl:text-base text-slate-200/90 leading-relaxed">
                            Masuk ke dashboard {{ $appName }} untuk memantau kontrak, progres konstruksi, penugasan tim, dan arsip dokumen dalam satu pintu.
                        </p>
                    </div>

                    <!-- Pratinjau UI Dashboard (HTML/Tailwind Tanpa Gambar Eksternal) -->
                    <div class="relative z-10 mt-8">
                        <div class="bg-white text-[#0F1B2D] rounded-2xl shadow-2xl p-5 border border-white/20">
                            <!-- Baris Atas: 3 Kartu Ringkasan -->
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="rounded-xl bg-[#0A2342] text-white p-3.5">
                                    <p class="text-[11px] text-slate-300">Proyek Aktif</p>
                                    <p class="text-xl font-bold mt-1">24 Proyek</p>
                                    <span class="inline-block mt-1.5 text-[10px] px-2 py-0.5 rounded-full bg-[#14B8A6]/20 text-teal-300">
                                        +4 Bulan Ini
                                    </span>
                                </div>

                                <div class="rounded-xl bg-[#F5F8FA] border border-[#E3E8EF] p-3.5">
                                    <p class="text-[11px] text-gray-500">Progres Lapangan</p>
                                    <p class="text-xl font-bold text-[#0A2342] mt-1">82,4%</p>
                                    <svg viewBox="0 0 100 24" class="w-full h-5 mt-1 stroke-[#14B8A6] fill-none" stroke-width="2.5">
                                        <path d="M2 20 Q 22 8, 40 14 T 75 6 T 98 3" stroke-linecap="round" />
                                    </svg>
                                </div>

                                <div class="rounded-xl bg-[#F5F8FA] border border-[#E3E8EF] p-3.5">
                                    <p class="text-[11px] text-gray-500">Kontrak &amp; Arsip</p>
                                    <p class="text-xl font-bold text-[#0A2342] mt-1">148 Dok</p>
                                    <span class="inline-block mt-1.5 text-[10px] text-emerald-600 font-semibold">
                                        Terverifikasi
                                    </span>
                                </div>
                            </div>

                            <!-- Baris Bawah: Mini Tabel Status Proyek -->
                            <div class="rounded-xl border border-[#E3E8EF] overflow-hidden">
                                <div class="bg-[#F5F8FA] px-3.5 py-2 flex items-center justify-between border-b border-[#E3E8EF]">
                                    <span class="text-xs font-semibold text-[#0F1B2D]">Monitoring Pekerjaan Terbaru</span>
                                    <span class="text-[11px] text-[#14B8A6] font-medium">Real-time</span>
                                </div>
                                <div class="divide-y divide-[#E3E8EF] text-xs">
                                    <div class="px-3.5 py-2.5 flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-[#0F1B2D]">Pembangunan Gedung Kantor Utama</p>
                                            <p class="text-[11px] text-gray-500">Kontrak #ARC-2026-01</p>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            85% Selesai
                                        </span>
                                    </div>
                                    <div class="px-3.5 py-2.5 flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-[#0F1B2D]">Renovasi Struktur Jembatan Wilayah II</p>
                                            <p class="text-[11px] text-gray-500">Kontrak #ARC-2026-04</p>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-sky-50 text-sky-700 border border-sky-200">
                                            Berjalan
                                        </span>
                                    </div>
                                    <div class="px-3.5 py-2.5 flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-[#0F1B2D]">Audit Teknis &amp; Addendum Tahap 2</p>
                                            <p class="text-[11px] text-gray-500">Kontrak #ARC-2026-07</p>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            Review
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kartu Melayang Kecil di Pojok Kanan Bawah -->
                        <div class="hidden xl:flex items-center gap-3 bg-white text-[#0F1B2D] rounded-2xl shadow-xl px-4 py-3 border border-[#E3E8EF] w-60 ml-auto -mt-6 mr-4 relative z-20">
                            <div class="w-10 h-10 rounded-full bg-teal-50 border-2 border-[#14B8A6] flex items-center justify-center text-xs font-bold text-[#0A2342] shrink-0">
                                RBAC
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#0F1B2D]">Keamanan Berlapis</p>
                                <p class="text-[11px] text-gray-500">Admin, Manajer &amp; Karyawan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>
