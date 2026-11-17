
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Progress Proyek
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm rounded-lg">

                @if (session('success'))
                    <div class="mb-6 rounded-lg bg-green-100 px-4 py-3 text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-lg bg-red-100 px-4 py-3 text-red-800">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">
                        Informasi Laporan Progress
                    </h3>

                    <a href="{{ route('progress-proyek.index') }}"
                       class="text-blue-600 hover:underline">
                        Kembali
                    </a>
                </div>

                <div class="mb-6 flex gap-3">
                    <a href="{{ route('progress-proyek.edit', $progress) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                        Edit Laporan
                    </a>
                    <form method="POST" action="{{ route('progress-proyek.destroy', $progress) }}" onsubmit="return confirm('Hapus laporan progress ini beserta dokumentasinya?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                            Hapus Laporan
                        </button>
                    </form>
                </div>

                <div class="space-y-4">

                    <div>
                        <p class="text-sm text-gray-500">ID Proyek</p>
                        <p class="font-semibold">
                            {{ $progress->proyek?->nama_proyek ?? 'Belum tersedia' }}
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

                    <hr>

                    <div>
                        <h4 class="mb-4 text-lg font-bold text-gray-800">Dokumentasi Progress</h4>

                        <form method="POST"
                              action="{{ route('progress-proyek.dokumentasi.store', ['progress_proyek' => $progress]) }}"
                              enctype="multipart/form-data"
                              class="mb-6 space-y-4 rounded-lg bg-gray-50 p-4">
                            @csrf

                            <div>
                                <label for="dokumentasi_file" class="block text-sm font-medium text-gray-700">
                                    Foto atau PDF
                                </label>
                                <input id="dokumentasi_file"
                                       name="dokumentasi_file"
                                       type="file"
                                       accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf"
                                       required
                                       class="mt-1 block w-full rounded-lg border-gray-300">
                                <p class="mt-1 text-xs text-gray-500">Format JPG, PNG, atau PDF; maksimal 10 MB.</p>
                            </div>

                            <div>
                                <label for="keterangan" class="block text-sm font-medium text-gray-700">
                                    Keterangan (opsional)
                                </label>
                                <textarea id="keterangan" name="keterangan" rows="2" class="mt-1 block w-full rounded-lg border-gray-300">{{ old('keterangan') }}</textarea>
                            </div>

                            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                                Unggah Dokumentasi
                            </button>
                        </form>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-100 text-gray-600">
                                    <tr>
                                        <th class="p-3">Nama File</th>
                                        <th class="p-3">Tanggal Upload</th>
                                        <th class="p-3">Keterangan</th>
                                        <th class="p-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($progress->dokumentasi as $dokumentasi)
                                        <tr class="border-b">
                                            <td class="p-3">{{ $dokumentasi->nama_file_asli }}</td>
                                            <td class="p-3">{{ $dokumentasi->created_at?->format('d-m-Y H:i') }}</td>
                                            <td class="p-3">{{ $dokumentasi->keterangan ?? '-' }}</td>
                                            <td class="p-3 space-x-3 whitespace-nowrap">
                                                <a href="{{ route('progress-proyek.dokumentasi.view', ['progress_proyek' => $progress, 'dokumentasi' => $dokumentasi]) }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline">Lihat</a>
                                                <a href="{{ route('progress-proyek.dokumentasi.download', ['progress_proyek' => $progress, 'dokumentasi' => $dokumentasi]) }}" class="text-blue-600 hover:underline">Unduh</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-6 text-center text-gray-500">Belum ada dokumentasi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
