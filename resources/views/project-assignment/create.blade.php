<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Penugasan Proyek</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form action="{{ route('project-assignment.store') }}" method="POST" class="space-y-4">
                    @csrf
                    @include('project-assignment.partials.form', ['assignment' => null])
                    <div class="flex gap-3">
                        <button class="rounded bg-blue-600 px-4 py-2 font-semibold text-white">Simpan Penugasan</button>
                        <a href="{{ route('project-assignment.index') }}" class="px-4 py-2 text-gray-700 hover:underline">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
