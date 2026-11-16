<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Data Proyek') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4">
                    <div class="text-sm text-indigo-700">
                        @switch(session('status'))
                            @case('proyek-created') {{ __('Data proyek berhasil ditambahkan.') }} @break
                            @case('proyek-updated') {{ __('Data proyek berhasil diperbarui.') }} @break
                            @case('proyek-deleted') {{ __('Data proyek berhasil dihapus.') }} @break
                        @endswitch
                    </div>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div><h3 class="text-lg font-medium text-gray-900">{{ __('Daftar Proyek') }}</h3></div>
                <div>
                    <a href="{{ route('proyek.create') }}" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Registrasi Proyek Baru') }}</a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow sm:rounded-lg border border-gray-200">
                <div class="px-4 py-5 sm:px-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Kode Proyek') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nama Proyek') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Klien') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Jenis Pekerjaan') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Tanggal') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="relative px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($proyek as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->kode_proyek }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->nama_proyek }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($item->klien)->nama_klien ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($item->jenisPekerjaan)->nama_pekerjaan ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} @if ($item->tanggal_selesai) s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }} @endif</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="inline-flex px-2 py-1 text-xs font-semibold leading-full rounded-full @if ($item->status == 'Selesai') bg-green-100 text-green-800 @elseif ($item->status == 'Proses') bg-blue-100 text-blue-800 @elseif ($item->status == 'Ditunda') bg-yellow-100 text-yellow-800 @elseif ($item->status == 'Batal') bg-red-100 text-red-800 @else bg-gray-100 text-gray-800 @endif">{{ ucfirst($item->status) }}</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('proyek.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                        <form action="{{ route('proyek.destroy', $item) }}" method="POST" class="inline-block">@csrf @method('DELETE') <button type="submit" class="text-red-600 hover:text-red-900 ml-3" onclick="return confirm('{{ __('Apakah Anda yakin ingin menghapus proyek ini?') }}')">{{ __('Hapus') }}</button></form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('Belum ada data proyek.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $proyek->links() }}
            </div>
        </div>
    </div>
</x-app-layout>