<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#0F766E]">Master Data Jenis Pekerjaan</p>
                <h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">{{ $jenisPekerjaan->nama_pekerjaan }}</h2>
            </div>
            <a href="{{ route('jenis-pekerjaan.edit', $jenisPekerjaan) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0A2342] px-5 text-sm font-semibold text-white hover:bg-[#12385E]">Edit Kategori</a>
        </div>
    </x-slot>

    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <section class="grid gap-4 sm:grid-cols-[minmax(0,2fr)_minmax(200px,1fr)]">
            <div class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#718096]">Deskripsi</p>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-[#43546A]">{{ $jenisPekerjaan->deskripsi ?: 'Deskripsi belum diisi.' }}</p>
            </div>
            <div class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                <p class="text-sm font-medium text-[#5B6B80]">Proyek terkait</p>
                <p class="mt-2 text-3xl font-bold text-[#0A2342]">{{ number_format($jumlahProyek) }}</p>
                <a href="{{ route('proyek.create', ['jenis_pekerjaan_id' => $jenisPekerjaan->id]) }}" class="mt-4 inline-flex min-h-10 items-center justify-center rounded-xl bg-[#0F766E] px-4 text-sm font-semibold text-white hover:bg-[#115E59]">Daftarkan Proyek</a>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
            <div class="border-b border-[#E3E8EF] px-5 py-4">
                <h3 class="font-bold text-[#0A2342]">Proyek dengan Kategori Ini</h3>
            </div>
            <div class="divide-y divide-[#E3E8EF]">
                @forelse ($proyek as $item)
                    <a href="{{ route('proyek.show', $item) }}" class="flex flex-col gap-2 px-5 py-4 transition hover:bg-[#F8FAFC] sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-semibold text-[#0A2342]">{{ $item->nama_proyek }}</p>
                            <p class="mt-1 text-xs text-[#718096]">{{ $item->kode_proyek }} · {{ $item->klien?->nama_klien ?? 'Klien belum tersedia' }}</p>
                        </div>
                        <span class="inline-flex w-fit rounded-full bg-[#E6F4F3] px-3 py-1 text-xs font-semibold text-[#0F766E]">{{ $item->status }}</span>
                    </a>
                @empty
                    <p class="px-5 py-10 text-center text-sm text-[#718096]">Belum ada proyek dengan jenis pekerjaan ini.</p>
                @endforelse
            </div>
            <div class="border-t border-[#E3E8EF] px-4 py-4 sm:px-5">{{ $proyek->links() }}</div>
        </section>
        <a href="{{ route('jenis-pekerjaan.index') }}" class="inline-flex min-h-10 items-center text-sm font-semibold text-[#2A6F97] hover:text-[#0A2342]">Kembali ke Jenis Pekerjaan</a>
    </div>
</x-app-layout>
