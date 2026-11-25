<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Tugas Proyek</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                @if (session('success'))
                    <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">{{ session('success') }}</div>
                @endif
                <div class="mb-4 flex justify-between">
                    <h3 class="text-lg font-semibold">{{ $projectTask->task_name }}</h3>
                    <a href="{{ route('project-task.edit', $projectTask) }}" class="text-indigo-700 hover:underline">Edit</a>
                </div>
                <dl class="space-y-3">
                    <div><dt class="text-sm text-gray-500">Penugasan</dt><dd>{{ $projectTask->projectAssignment?->proyek?->nama_proyek }} — {{ $projectTask->projectAssignment?->user?->name }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Deskripsi</dt><dd>{{ $projectTask->task_description ?: '-' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Tanggal mulai</dt><dd>{{ $projectTask->start_date?->format('d-m-Y') ?? '-' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Deadline</dt><dd>{{ $projectTask->due_date->format('d-m-Y') }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Prioritas</dt><dd>{{ ucfirst($projectTask->priority) }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Status</dt><dd>{{ str_replace('_', ' ', ucfirst($projectTask->status)) }}</dd></div>
                </dl>
                <div class="mt-5 flex gap-4">
                    <a href="{{ route('project-task.index', ['project_assignment_id' => $projectTask->project_assignment_id]) }}" class="text-blue-700 hover:underline">Kembali ke daftar tugas</a>
                    <a href="{{ route('project-assignment.show', $projectTask->project_assignment_id) }}" class="text-gray-700 hover:underline">Detail penugasan</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
