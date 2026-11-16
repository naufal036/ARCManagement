
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Progress Reporting
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">
                            Daftar Progress Proyek
                        </h3>
                        <p class="text-sm text-gray-500">
                            Pantau perkembangan pekerjaan proyek.
                        </p>
                    </div>

                    <a href="{{ route('progress-proyek.create') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        + Tambah Progress
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="p-3">No.</th>
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">Proyek</th>
                                <th class="p-3">Persentase</th>
                                <th class="p-3">Uraian Pekerjaan</th>
                                <th class="p-3">Pelapor</th>
                                <th class="p-3">Aksi</th>
                            </tr>
                        </thead>

                        
                        <tbody>
                            @forelse ($progressProyek as $index => $progress)
                                <tr class="border-b hover:bg-gray-50">

                                    <td class="p-3">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="p-3">
                                        {{ $progress->tanggal_laporan?->format('d-m-Y') }}
                                    </td>

                                    <td class="p-3">
                                        Proyek #{{ $progress->proyek_id ?? '-' }}
                                    </td>

                                    <td class="p-3">
                                        {{ $progress->persentase_progress }}%
                                    </td>

                                    <td class="p-3">
                                        {{ Str::limit($progress->uraian_pekerjaan, 50) }}
                                    </td>

                                    <td class="p-3">
                                        {{ $progress->user?->name ?? '-' }}
                                    </td>

                                    <td class="p-3">
                                        <a href="{{ route('progress-proyek.show', $progress->id) }}"
                                        class="text-blue-600 hover:underline">
                                            Detail
                                        </a>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-500">
                                        Belum ada data progress proyek.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
