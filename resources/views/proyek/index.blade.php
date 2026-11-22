<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#0F766E]">Registrasi dan Data Induk</p>
                <h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">Proyek</h2>
            </div>
            <a href="{{ route('proyek.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0A2342] px-5 text-sm font-semibold text-white transition hover:bg-[#12385E] focus:outline-none focus:ring-2 focus:ring-[#14B8A6] focus:ring-offset-2">Registrasi Proyek</a>
        </div>
    </x-slot>

    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        @if (session('status'))
            <div role="status" class="rounded-xl border border-[#B7E4DC] bg-[#E6F4F3] px-4 py-3 text-sm font-medium text-[#0F766E]">
                @switch(session('status'))
                    @case('proyek-created') Proyek berhasil didaftarkan. @break
                    @case('proyek-updated') Data proyek berhasil diperbarui. @break
                    @case('proyek-deleted') Proyek berhasil dihapus. @break
                @endswitch
            </div>
        @endif
        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <section aria-label="Statistik proyek" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <article class="flex min-h-36 items-start justify-between gap-4 rounded-2xl border border-[#E3E8EF] border-l-4 border-l-[#0A2342] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] transition hover:-translate-y-0.5 hover:shadow-[0_12px_28px_rgba(15,27,45,0.09)]">
                <div>
                    <p class="text-sm font-medium text-[#5B6B80]">Total Proyek</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-[#0A2342]">{{ number_format($totalProyek) }}</p>
                    <p class="mt-1 text-xs text-[#718096]">Seluruh proyek terdaftar</p>
                </div>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#EEF3F8] text-[#0A2342]">
                    <svg aria-hidden="true" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4" />
                        <path d="M9 9v.01M9 12v.01M9 15v.01M9 18v.01M14 12v.01M14 15v.01M14 18v.01" />
                    </svg>
                </span>
            </article>
            <article class="flex min-h-36 items-start justify-between gap-4 rounded-2xl border border-[#E3E8EF] border-l-4 border-l-[#8793A2] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] transition hover:-translate-y-0.5 hover:shadow-[0_12px_28px_rgba(15,27,45,0.09)]">
                <div>
                    <p class="text-sm font-medium text-[#5B6B80]">Perencanaan</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-[#0A2342]">{{ number_format($proyekPerencanaan) }}</p>
                    <p class="mt-1 text-xs text-[#718096]">Proyek yang sedang direncanakan</p>
                </div>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#F1F4F7] text-[#5B6B80]">
                    <svg aria-hidden="true" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2" />
                        <path d="M16 3v4M8 3v4M3 10h18M8 14h3M8 17h6" />
                    </svg>
                </span>
            </article>
            <article class="flex min-h-36 items-start justify-between gap-4 rounded-2xl border border-[#E3E8EF] border-l-4 border-l-[#14B8A6] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] transition hover:-translate-y-0.5 hover:shadow-[0_12px_28px_rgba(15,27,45,0.09)]">
                <div>
                    <p class="text-sm font-medium text-[#5B6B80]">Proses</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-[#0A2342]">{{ number_format($proyekProses) }}</p>
                    <p class="mt-1 text-xs text-[#718096]">Proyek yang sedang berjalan</p>
                </div>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#E6F4F3] text-[#0F766E]">
                    <svg aria-hidden="true" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19V5M4 19h16" />
                        <path d="m7 15 4-4 3 2 5-6" />
                        <path d="M15 7h4v4" />
                    </svg>
                </span>
            </article>
        </section>

        <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
            <form method="GET" action="{{ route('proyek.index') }}" class="grid gap-3 border-b border-[#E3E8EF] p-4 sm:grid-cols-2 xl:grid-cols-6 sm:p-5">
                <label class="sr-only" for="search-proyek">Cari proyek</label>
                <input id="search-proyek" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama atau kode..." class="min-h-11 rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                <label class="sr-only" for="filter-klien">Pilih klien</label>
                <select id="filter-klien" name="klien_id" class="min-h-11 rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                    <option value="">Semua klien</option>
                    @foreach ($klien as $item)
                        <option value="{{ $item->id }}" @selected(($filters['klien_id'] ?? '') == $item->id)>{{ $item->nama_klien }}</option>
                    @endforeach
                </select>
                <label class="sr-only" for="filter-jenis">Pilih jenis pekerjaan</label>
                <select id="filter-jenis" name="jenis_pekerjaan_id" class="min-h-11 rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                    <option value="">Semua jenis pekerjaan</option>
                    @foreach ($jenisPekerjaan as $item)
                        <option value="{{ $item->id }}" @selected(($filters['jenis_pekerjaan_id'] ?? '') == $item->id)>{{ $item->nama_pekerjaan }}</option>
                    @endforeach
                </select>
                <label class="sr-only" for="filter-status">Pilih status</label>
                <select id="filter-status" name="status" class="min-h-11 rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <button class="min-h-11 rounded-xl bg-[#0A2342] px-5 text-sm font-semibold text-white transition hover:bg-[#12385E] focus:outline-none focus:ring-2 focus:ring-[#14B8A6]">Terapkan</button>
                <a href="{{ route('proyek.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#D7DFE8] px-4 text-sm font-semibold text-[#43546A] hover:bg-[#F5F8FA]">Reset</a>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E3E8EF] text-sm">
                    <thead class="bg-[#F8FAFC] text-left text-xs uppercase tracking-wide text-[#5B6B80]">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Proyek</th>
                            <th class="px-5 py-3 font-semibold">Klien / Jenis</th>
                            <th class="px-5 py-3 font-semibold">Jadwal</th>
                            <th class="px-5 py-3 font-semibold">Anggaran</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E3E8EF]">
                        @forelse ($proyek as $item)
                            <tr class="transition hover:bg-[#F8FAFC]">
                                <td class="px-5 py-4">
                                    <a href="{{ route('proyek.show', $item) }}" class="font-semibold text-[#0A2342] hover:text-[#0F766E]">{{ $item->nama_proyek }}</a>
                                    <p class="mt-1 text-xs text-[#718096]">{{ $item->kode_proyek }}</p>
                                </td>
                                <td class="px-5 py-4 text-[#43546A]">
                                    <p>{{ $item->klien?->nama_klien ?? 'Klien tidak tersedia' }}</p>
                                    <p class="mt-1 text-xs text-[#718096]">{{ $item->jenisPekerjaan?->nama_pekerjaan ?? 'Jenis tidak tersedia' }}</p>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-xs text-[#43546A]">
                                    <p>{{ $item->tanggal_mulai?->format('d M Y') }}</p>
                                    <p class="mt-1 text-[#718096]">Target: {{ $item->tanggal_selesai?->format('d M Y') ?? 'Belum ditentukan' }}</p>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-[#43546A]">{{ $item->anggaran !== null ? 'Rp '.number_format((float) $item->anggaran, 0, ',', '.') : '—' }}</td>
                                <td class="px-5 py-4"><span class="inline-flex rounded-full bg-[#E6F4F3] px-3 py-1 text-xs font-semibold text-[#0F766E]">{{ $item->status }}</span></td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('proyek.edit', $item) }}" class="font-semibold text-[#2A6F97] hover:text-[#0A2342]">Edit</a>
                                        <form action="{{ route('proyek.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus proyek ini? Data proyek yang terhubung akan dipertahankan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-[#718096]">Tidak ada proyek yang cocok dengan filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[#E3E8EF] px-4 py-4 sm:px-5">{{ $proyek->links() }}</div>
        </section>
    </div>
</x-app-layout>
