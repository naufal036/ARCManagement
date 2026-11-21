<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#0F766E]">Master Data Klien</p>
                <h2 class="text-2xl font-bold tracking-tight text-[#0A2342]">{{ $klien->nama_klien }}</h2>
            </div>
            <a href="{{ route('klien.edit', $klien) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0A2342] px-5 text-sm font-semibold text-white hover:bg-[#12385E]">Edit Klien</a>
        </div>
    </x-slot>

    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([['label' => 'Nama Klien', 'value' => $klien->nama_klien], ['label' => 'Email', 'value' => $klien->email ?: 'Belum diisi'], ['label' => 'Telepon', 'value' => $klien->no_telepon ?: 'Belum diisi'], ['label' => 'Jumlah Proyek', 'value' => number_format($jumlahProyek)]] as $detail)
                <div class="rounded-2xl border border-[#E3E8EF] bg-white p-5 shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#718096]">{{ $detail['label'] }}</p>
                    <p class="mt-2 break-words text-sm font-semibold text-[#0A2342]">{{ $detail['value'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
            <div class="border-b border-[#E3E8EF] px-5 py-4">
                <h3 class="font-bold text-[#0A2342]">Informasi Klien</h3>
                <p class="mt-1 text-sm text-[#5B6B80]">{{ $klien->alamat ?: 'Alamat belum diisi.' }}</p>
            </div>
            <div class="flex flex-col gap-3 p-5 sm:flex-row">
                <a href="{{ route('klien.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#D7DFE8] px-4 text-sm font-semibold text-[#43546A] hover:bg-[#F5F8FA]">Kembali ke Klien</a>
                <a href="{{ route('proyek.create', ['klien_id' => $klien->id]) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0F766E] px-4 text-sm font-semibold text-white hover:bg-[#115E59]">Daftarkan Proyek</a>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-[#E3E8EF] bg-white shadow-[0_8px_24px_rgba(15,27,45,0.05)]">
            <div class="border-b border-[#E3E8EF] px-5 py-4">
                <h3 class="font-bold text-[#0A2342]">Riwayat Proyek</h3>
                <p class="mt-1 text-sm text-[#5B6B80]">Daftar proyek yang terdaftar untuk klien ini.</p>
            </div>
            <div class="divide-y divide-[#E3E8EF]">
                @forelse ($proyek as $item)
                    <a href="{{ route('proyek.show', $item) }}" class="flex flex-col gap-2 px-5 py-4 transition hover:bg-[#F8FAFC] sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-semibold text-[#0A2342]">{{ $item->nama_proyek }}</p>
                            <p class="mt-1 text-xs text-[#718096]">{{ $item->kode_proyek }} · {{ $item->jenisPekerjaan?->nama_pekerjaan ?? 'Jenis belum tersedia' }}</p>
                        </div>
                        <span class="inline-flex w-fit rounded-full bg-[#E6F4F3] px-3 py-1 text-xs font-semibold text-[#0F766E]">{{ $item->status }}</span>
                    </a>
                @empty
                    <p class="px-5 py-10 text-center text-sm text-[#718096]">Klien ini belum memiliki proyek.</p>
                @endforelse
            </div>
            <div class="border-t border-[#E3E8EF] px-4 py-4 sm:px-5">{{ $proyek->links() }}</div>
        </section>
    </div>
</x-app-layout>
