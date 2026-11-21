<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Penugasan Proyek</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div><span class="text-sm text-gray-500">Proyek</span><p class="font-semibold">{{ $projectAssignment->proyek?->nama_proyek ?? '-' }}</p></div>
                <div><span class="text-sm text-gray-500">Anggota</span><p>{{ $projectAssignment->user?->name ?? '-' }}</p></div>
                <div><span class="text-sm text-gray-500">Peran</span><p>{{ $projectAssignment->role_in_project }}</p></div>
                <div><span class="text-sm text-gray-500">Tanggal Penugasan</span><p>{{ $projectAssignment->assigned_date }}</p></div>
                <a href="{{ route('project-assignment.index') }}" class="text-blue-600 hover:underline">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
