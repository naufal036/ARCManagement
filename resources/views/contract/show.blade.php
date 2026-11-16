<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Kontrak</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div><span class="text-sm text-gray-500">Nomor Kontrak</span><p class="font-semibold">{{ $contract->contract_number }}</p></div>
                <div><span class="text-sm text-gray-500">Nama Kontrak</span><p>{{ $contract->contract_name }}</p></div>
                <div><span class="text-sm text-gray-500">Proyek</span><p>{{ $contract->proyek?->nama_proyek ?? '-' }}</p></div>
                <div><span class="text-sm text-gray-500">Nilai Kontrak</span><p>Rp {{ number_format((float) $contract->contract_value, 0, ',', '.') }}</p></div>
                <div><span class="text-sm text-gray-500">Status</span><p>{{ $contract->status }}</p></div>
                <a href="{{ route('contract.index') }}" class="text-blue-600 hover:underline">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
