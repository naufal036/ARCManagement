
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Progress Proyek
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm rounded-lg">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">
                        Informasi Laporan Progress
                    </h3>

                    <a href="{{ route('progress-proyek.index') }}"
                       class="text-blue-600 hover:underline">
                        Kembali
                    </a>
                </div>

                <div class="space-y-4">

                    <div>
                        <p class="text-sm text-gray-500">ID Proyek</p>
                        <p class="font-semibold">
                            {{ $progress->proyek_id ?? 'Belum tersedia' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Tanggal Laporan</p>
                        <p class="font-semibold">
                            {{ $progress->tanggal_laporan?->format('d-m-Y') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Pelapor</p>
                        <p class="font-semibold">
                            {{ $progress->user?->name ?? 'Belum tersedia' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Persentase Progress</p>
                        <p class="font-semibold text-blue-600">
                            {{ $progress->persentase_progress }}%
                        </p>
                    </div>

                    <hr>

                    <div>
                        <p class="text-sm text-gray-500">Uraian Pekerjaan</p>
                        <p>{{ $progress->uraian_pekerjaan }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Kendala</p>
                        <p>{{ $progress->kendala ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Solusi</p>
                        <p>{{ $progress->solusi ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Catatan</p>
                        <p>{{ $progress->catatan ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Rencana Selanjutnya</p>
                        <p>{{ $progress->rencana_selanjutnya ?? '-' }}</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
