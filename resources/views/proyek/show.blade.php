<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Proyek</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div><span class="text-sm text-gray-500">Kode Proyek</span><p class="font-semibold">{{ $proyek->kode_proyek }}</p></div>
                <div><span class="text-sm text-gray-500">Nama Proyek</span><p>{{ $proyek->nama_proyek }}</p></div>
                <div><span class="text-sm text-gray-500">Klien</span><p>{{ $proyek->klien?->nama_klien ?? '-' }}</p></div>
                <div><span class="text-sm text-gray-500">Jenis Pekerjaan</span><p>{{ $proyek->jenisPekerjaan?->nama_pekerjaan ?? '-' }}</p></div>
                <div><span class="text-sm text-gray-500">Periode</span><p>{{ $proyek->tanggal_mulai }} s/d {{ $proyek->tanggal_selesai ?? '-' }}</p></div>
                <div><span class="text-sm text-gray-500">Status</span><p>{{ $proyek->status }}</p></div>
                <div><span class="text-sm text-gray-500">Deskripsi</span><p>{{ $proyek->deskripsi ?? '-' }}</p></div>

                <div>
                    <h3 class="mb-2 text-lg font-semibold">Progress Terbaru</h3>
                    @forelse ($proyek->progressProyek as $progress)
                        <a href="{{ route('progress-proyek.show', $progress) }}" class="block rounded border p-3 hover:bg-gray-50">
                            {{ $progress->tanggal_laporan?->format('d-m-Y') }} — {{ $progress->persentase_progress }}%
                        </a>
                    @empty
                        <p class="text-gray-500">Belum ada laporan progress.</p>
                    @endforelse
                </div>

                <a href="{{ route('proyek.index') }}" class="text-blue-600 hover:underline">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
