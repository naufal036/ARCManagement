@extends('layouts.app')

@section('header', 'Pengaturan Sistem')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Kartu Atas: Konfigurasi Aplikasi -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-[#E3E8EF]">
            <h3 class="text-lg font-bold text-[#0F1B2D] mb-4">Konfigurasi Umum</h3>

            <form action="{{ route('settings.update') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="app_name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Aplikasi</label>
                    <input type="text" name="app_name" id="app_name" value="{{ $appName->config_value }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#14B8A6] focus:ring focus:ring-[#14B8A6]/20" required>
                    <p class="text-xs text-gray-500 mt-1.5">Nama ini akan muncul di pojok kiri atas aplikasi (Sidebar).</p>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="bg-[#0A2342] text-white px-5 py-2.5 rounded-lg hover:bg-[#133660] transition font-medium text-sm">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        <!-- Kartu Bawah: Log Aktivitas Semua Pengguna -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-[#E3E8EF]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-5">
                <div>
                    <h3 class="text-lg font-bold text-[#0F1B2D]">Log Aktivitas Sistem</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Mencatat riwayat aktivitas seluruh pengguna (Admin, Manajer, dan Karyawan).</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#F5F8FA] text-[#0A2342] border border-[#E3E8EF]">
                    Total: {{ $logs->total() }} Aktivitas
                </span>
            </div>

            <div class="overflow-x-auto border border-[#E3E8EF] rounded-xl">
                <table class="min-w-full divide-y divide-[#E3E8EF] text-sm">
                    <thead class="bg-[#F5F8FA] text-[#5B6B80] text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Waktu</th>
                            <th class="px-4 py-3 text-left font-semibold">Pengguna</th>
                            <th class="px-4 py-3 text-left font-semibold">Aktivitas</th>
                            <th class="px-4 py-3 text-left font-semibold">Keterangan</th>
                            <th class="px-4 py-3 text-left font-semibold">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E3E8EF] bg-white">
                        @forelse ($logs as $log)
                            @php
                                $badgeClass = match ($log->activity) {
                                    'Login' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Logout' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'Tambah Pengguna' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'Update Pengguna' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-sky-50 text-sky-700 border-sky-200',
                                };
                            @endphp
                            <tr class="hover:bg-[#F5F8FA]/60 transition">
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">
                                    {{ $log->created_at->format('d M Y, H:i') }} WIB
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-medium text-[#0F1B2D]">{{ $log->user->name ?? 'Pengguna Terhapus' }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->user->role->name ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full border {{ $badgeClass }}">
                                        {{ $log->activity }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $log->description ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap font-mono text-xs text-gray-500">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                    Belum ada aktivitas yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
