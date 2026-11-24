<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#0F766E]">Master Data</p>
                <h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">Jenis Pekerjaan</h2>
            </div>
            <a href="{{ route('jenis-pekerjaan.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0A2342] px-5 text-sm font-semibold text-white transition hover:bg-[#12385E] focus:outline-none focus:ring-2 focus:ring-[#14B8A6] focus:ring-offset-2">
                Tambah Jenis Pekerjaan
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        @if (session('status'))
            <div role="status" class="rounded-xl border border-[#B7E4DC] bg-[#E6F4F3] px-4 py-3 text-sm font-medium text-[#0F766E]">
                @switch(session('status'))
                    @case('jenis-pekerjaan-created') Jenis pekerjaan berhasil ditambahkan. @break
                    @case('jenis-pekerjaan-updated') Jenis pekerjaan berhasil diperbarui. @break
                    @case('jenis-pekerjaan-deleted') Jenis pekerjaan berhasil dihapus. @break
                    @case('jenis-pekerjaan-deactivated') Jenis pekerjaan digunakan proyek, sehingga dinonaktifkan untuk menjaga riwayat. @break
                    @case('jenis-pekerjaan-toggled') Status jenis pekerjaan berhasil diperbarui. @break
                @endswitch
            </div>
        @endif
        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <section aria-label="Statistik jenis pekerjaan" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <article class="flex min-h-36 items-start justify-between gap-4 rounded-2xl border border-[#E3E8EF] border-l-4 border-l-[#0A2342] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] transition hover:-translate-y-0.5 hover:shadow-[0_12px_28px_rgba(15,27,45,0.09)]">
                <div>
                    <p class="text-sm font-medium text-[#5B6B80]">Total Jenis Pekerjaan</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-[#0A2342]">{{ number_format($totalJenisPekerjaan) }}</p>
                    <p class="mt-1 text-xs text-[#718096]">Seluruh jenis pekerjaan terdaftar</p>
                </div>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#EEF3F8] text-[#0A2342]">
                    <svg aria-hidden="true" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 6h11M9 12h11M9 18h11" />
                        <path d="M4 6h.01M4 12h.01M4 18h.01" />
                    </svg>
                </span>
            </article>
            <article class="flex min-h-36 items-start justify-between gap-4 rounded-2xl border border-[#E3E8EF] border-l-4 border-l-[#14B8A6] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)] transition hover:-translate-y-0.5 hover:shadow-[0_12px_28px_rgba(15,27,45,0.09)]">
                <div>
                    <p class="text-sm font-medium text-[#5B6B80]">Digunakan pada Proyek</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-[#0A2342]">{{ number_format($jenisDenganProyek) }}</p>
                    <p class="mt-1 text-xs text-[#718096]">Jenis pekerjaan dengan minimal satu proyek</p>
                </div>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#E6F4F3] text-[#0F766E]">
                    <svg aria-hidden="true" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="7" width="18" height="14" rx="2" />
                        <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18M10 12v2h4v-2" />
                    </svg>
                </span>
            </article>
        </section>

        <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
            <form method="GET" action="{{ route('jenis-pekerjaan.index') }}" class="grid gap-3 border-b border-[#E3E8EF] p-4 sm:grid-cols-[minmax(0,1fr)_220px_auto_auto] sm:p-5">
                <label class="sr-only" for="search-jenis">Cari jenis pekerjaan</label>
                <input id="search-jenis" type="search" name="search" value="{{ $search }}" placeholder="Cari kategori atau deskripsi..." class="min-h-11 rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                <label class="sr-only" for="project-filter">Filter berdasarkan proyek</label>
                <select id="project-filter" name="project_filter" class="min-h-11 rounded-xl border-[#D7DFE8] text-sm focus:border-[#14B8A6] focus:ring-[#14B8A6]">
                    <option value="all" @selected($projectFilter === 'all')>Semua kategori</option>
                    <option value="with_projects" @selected($projectFilter === 'with_projects')>Digunakan proyek</option>
                    <option value="without_projects" @selected($projectFilter === 'without_projects')>Belum digunakan</option>
                </select>
                <button class="min-h-11 rounded-xl bg-[#0A2342] px-5 text-sm font-semibold text-white transition hover:bg-[#12385E] focus:outline-none focus:ring-2 focus:ring-[#14B8A6]">Terapkan</button>
                <a href="{{ route('jenis-pekerjaan.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#D7DFE8] px-4 text-sm font-semibold text-[#43546A] hover:bg-[#F5F8FA]">Reset</a>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E3E8EF] text-sm">
                    <thead class="bg-[#F8FAFC] text-left text-xs uppercase tracking-wide text-[#5B6B80]">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Kategori</th>
                            <th class="px-5 py-3 font-semibold">Deskripsi</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold">Proyek</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E3E8EF]">
                        @forelse ($jenisPekerjaan as $item)
                            <tr class="transition hover:bg-[#F8FAFC]">
                                <td class="px-5 py-4"><a href="{{ route('jenis-pekerjaan.show', $item) }}" class="font-semibold text-[#0A2342] hover:text-[#0F766E]">{{ $item->nama_pekerjaan }}</a></td>
                                <td class="max-w-xl px-5 py-4 text-[#5B6B80]">{{ $item->deskripsi ?: 'Deskripsi belum diisi' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4"><span class="inline-flex rounded-full bg-[#E6F4F3] px-3 py-1 text-xs font-semibold text-[#0F766E]">{{ number_format($item->proyek_count) }} proyek</span></td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('jenis-pekerjaan.show', $item) }}" class="text-xs font-semibold text-[#0F766E] hover:text-[#0A2342]">Detail</a>
                                        <a href="{{ route('jenis-pekerjaan.edit', $item) }}" class="text-xs font-semibold text-[#2563EB] hover:text-[#1D4ED8]">Edit</a>
                                        <form action="{{ route('jenis-pekerjaan.toggle', $item) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-[#D97706] hover:text-[#B45309]">
                                                {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('jenis-pekerjaan.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data jenis pekerjaan ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-[#5B6B80]">Belum ada jenis pekerjaan yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($jenisPekerjaan->hasPages())
                <div class="border-t border-[#E3E8EF] px-5 py-4">
                    {{ $jenisPekerjaan->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
