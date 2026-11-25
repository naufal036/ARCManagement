<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Tugas Proyek</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form action="{{ route('project-task.update', $projectTask) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('project-task.partials.form', ['task' => $projectTask])
                    <div class="flex gap-3">
                        <button class="rounded bg-blue-600 px-4 py-2 font-semibold text-white">Simpan Perubahan</button>
                        <a href="{{ route('project-task.show', $projectTask) }}" class="px-4 py-2 text-gray-700 hover:underline">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
