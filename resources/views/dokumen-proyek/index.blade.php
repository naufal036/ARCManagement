<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ __('Dokumen Proyek') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="border-s-4 border-indigo-500 bg-indigo-50 p-4 text-sm text-indigo-700">
                    @switch(session('status'))
                        @case('dokumen-proyek-created')
                            {{ __('Dokumen berhasil ditambahkan. Unggah file versi pertama melalui halaman detail.') }}
                            @break
                        @case('dokumen-proyek-updated')
                            {{ __('Data dokumen berhasil diperbarui.') }}
                            @break
                        @case('dokumen-proyek-deleted')
                            {{ __('Dokumen berhasil dihapus.') }}
                            @break
                        @case('dokumen-proyek-deleted-files-retained')
                            {{ __('Data dokumen dihapus, tetapi sebagian file versi tidak dapat dihapus dari penyimpanan.') }}
                            @break
                    @endswitch
                </div>
            @endif

            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Daftar Dokumen Proyek') }}</h3>
                <div class="flex gap-3">
                    <a href="{{ route('kategori-dokumen.index') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50">{{ __('Kategori') }}</a>
                    <a href="{{ route('dokumen-proyek.create') }}" class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">{{ __('Tambah Dokumen') }}</a>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow">
                <div class="overflow-x-auto px-4 py-5 sm:px-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Nama Dokumen') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Proyek') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Kategori') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Versi Terbaru') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($dokumenProyek as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->nama_dokumen }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $item->proyek->nama_proyek }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $item->kategoriDokumen->nama_kategori }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $item->versiTerbaru ? 'v'.$item->versiTerbaru->nomor_versi : __('Belum ada versi') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <a href="{{ route('dokumen-proyek.show', $item) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Detail') }}</a>
                                        <a href="{{ route('dokumen-proyek.edit', $item) }}" class="ml-3 text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                        <form action="{{ route('dokumen-proyek.destroy', $item) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-3 text-red-600 hover:text-red-900" onclick="return confirm('{{ __('Hapus dokumen dan seluruh versinya?') }}')">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('Belum ada dokumen proyek.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $dokumenProyek->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
