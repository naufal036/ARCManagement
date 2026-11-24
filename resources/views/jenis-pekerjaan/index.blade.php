<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jenis Pekerjaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4">
                    <div class="text-sm text-indigo-700">
                        @switch(session('status'))
                            @case('jenis-pekerjaan-created')
                                {{ __('Data jenis pekerjaan berhasil ditambahkan.') }}
                                @break
                            @case('jenis-pekerjaan-updated')
                                {{ __('Data jenis pekerjaan berhasil diperbarui.') }}
                                @break
                            @case('jenis-pekerjaan-deleted')
                                {{ __('Data jenis pekerjaan berhasil dihapus.') }}
                                @break
                            @case('jenis-pekerjaan-deactivated')
                                {{ __('Jenis pekerjaan digunakan proyek, sehingga dinonaktifkan untuk menjaga riwayat.') }}
                                @break
                            @case('jenis-pekerjaan-toggled')
                                {{ __('Status jenis pekerjaan berhasil diperbarui.') }}
                                @break
                        @endswitch
                    </div>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Daftar Jenis Pekerjaan') }}</h3>
                </div>
                <div>
                    <a href="{{ route('jenis-pekerjaan.create') }}" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Tambah Jenis Pekerjaan') }}
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow sm:rounded-lg border border-gray-200">
                <div class="px-4 py-5 sm:px-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nama Pekerjaan') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Deskripsi') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="relative px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($jenisPekerjaan as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->nama_pekerjaan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->deskripsi ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span><span class="ml-2 text-xs text-gray-500">{{ $item->proyek_count }} proyek</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('jenis-pekerjaan.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                        <form action="{{ route('jenis-pekerjaan.toggle', $item) }}" method="POST" class="inline-block ml-3">@csrf<button type="submit" class="text-blue-600 hover:text-blue-900">{{ $item->is_active ? __('Nonaktifkan') : __('Aktifkan') }}</button></form>
                                        <form action="{{ route('jenis-pekerjaan.destroy', $item) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 ml-3" onclick="return confirm('{{ __('Apakah Anda yakin ingin menghapus jenis pekerjaan ini?') }}')">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('Belum ada data jenis pekerjaan.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $jenisPekerjaan->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
