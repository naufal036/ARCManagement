<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Klien</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div><span class="text-sm text-gray-500">Nama Klien</span><p class="font-semibold">{{ $klien->nama_klien }}</p></div>
                <div><span class="text-sm text-gray-500">Alamat</span><p>{{ $klien->alamat ?? '-' }}</p></div>
                <div><span class="text-sm text-gray-500">Telepon</span><p>{{ $klien->no_telepon ?? '-' }}</p></div>
                <div><span class="text-sm text-gray-500">Email</span><p>{{ $klien->email ?? '-' }}</p></div>
                <a href="{{ route('klien.index') }}" class="text-blue-600 hover:underline">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
