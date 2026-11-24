<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Kontrak Proyek</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">{{ session('error') }}</div>
                @endif

                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <form method="GET" action="{{ route('contract.index') }}" class="flex gap-2">
                        <input type="search" name="search" value="{{ $search }}" placeholder="Cari nomor, nama, atau proyek"
                            class="rounded border-gray-300 shadow-sm">
                        <button class="rounded bg-slate-700 px-4 py-2 text-white">Cari</button>
                    </form>
                    <a href="{{ route('contract.create') }}" class="rounded bg-blue-600 px-4 py-2 font-semibold text-white">+ Tambah Kontrak</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Nomor Kontrak</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Proyek</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Nilai</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($contracts as $contract)
                                <tr>
                                    <td class="px-4 py-3">{{ $contract->contract_number }}</td>
                                    <td class="px-4 py-3">{{ $contract->proyek?->nama_proyek ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $contract->contract_date->format('d-m-Y') }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format((float) $contract->contract_value, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($contract->status) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('contract.show', $contract) }}" class="text-blue-700 hover:underline">Detail</a>
                                        <a href="{{ route('contract.edit', $contract) }}" class="ml-2 text-indigo-700 hover:underline">Edit</a>
                                        <form action="{{ route('contract.destroy', $contract) }}" method="POST" class="ml-2 inline"
                                            onsubmit="return confirm('Yakin ingin menghapus kontrak ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-700 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada data kontrak.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $contracts->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
