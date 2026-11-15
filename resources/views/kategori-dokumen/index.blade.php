<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ __('Kategori Dokumen') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="border-s-4 border-indigo-500 bg-indigo-50 p-4 text-sm text-indigo-700">
                    @switch(session('status'))
                        @case('kategori-dokumen-created')
                            {{ __('Kategori dokumen berhasil ditambahkan.') }}
                            @break
                        @case('kategori-dokumen-updated')
                            {{ __('Kategori dokumen berhasil diperbarui.') }}
                            @break
                        @case('kategori-dokumen-deleted')
                            {{ __('Kategori dokumen berhasil dihapus.') }}
                            @break
                        @case('kategori-dokumen-in-use')
                            {{ __('Kategori tidak dapat dihapus karena masih digunakan oleh dokumen.') }}
                            @break
                    @endswitch
                </div>
            @endif

            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Daftar Kategori Dokumen') }}</h3>
                <a href="{{ route('kategori-dokumen.create') }}" class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                    {{ __('Tambah Kategori') }}
                </a>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow">
                <div class="overflow-x-auto px-4 py-5 sm:px-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Nama Kategori') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Deskripsi') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Jumlah Dokumen') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($kategoriDokumen as $item)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $item->nama_kategori }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $item->deskripsi ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $item->dokumen_proyek_count }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <a href="{{ route('kategori-dokumen.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                        <form action="{{ route('kategori-dokumen.destroy', $item) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-3 text-red-600 hover:text-red-900" onclick="return confirm('{{ __('Hapus kategori ini?') }}')">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('Belum ada kategori dokumen.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $kategoriDokumen->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
