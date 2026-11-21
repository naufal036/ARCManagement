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

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <p class="text-sm font-medium text-[#5B6B80]">Total Proyek</p>
                <p class="mt-2 text-3xl font-bold text-[#0A2342]">{{ number_format($totalProyek) }}</p>
            </div>
            @foreach ($jumlahProyekPerStatus as $stat)
                <div class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                    <p class="text-sm font-medium text-[#5B6B80]">{{ $stat->status }}</p>
                    <p class="mt-2 text-3xl font-bold text-[#0A2342]">{{ number_format($stat->total) }}</p>
                </div>
            @endforeach
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
