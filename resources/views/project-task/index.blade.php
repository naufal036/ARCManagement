<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Tugas Proyek</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                @if (session('success'))
                    <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">{{ session('success') }}</div>
                @endif
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <form method="GET" action="{{ route('project-task.index') }}" class="flex flex-wrap gap-2">
                        <select name="project_assignment_id" class="rounded border-gray-300">
                            <option value="">Semua penugasan</option>
                            @foreach ($assignments as $assignment)
                                <option value="{{ $assignment->id }}" @selected((string) ($filters['project_assignment_id'] ?? '') === (string) $assignment->id)>
                                    {{ $assignment->proyek?->nama_proyek }} — {{ $assignment->user?->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="status" class="rounded border-gray-300">
                            <option value="">Semua status</option>
                            @foreach (['pending', 'in_progress', 'completed', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                            @endforeach
                        </select>
                        <select name="priority" class="rounded border-gray-300">
                            <option value="">Semua prioritas</option>
                            @foreach (['low', 'medium', 'high'] as $priority)
                                <option value="{{ $priority }}" @selected(($filters['priority'] ?? '') === $priority)>{{ ucfirst($priority) }}</option>
                            @endforeach
                        </select>
                        <button class="rounded bg-slate-700 px-4 py-2 text-white">Filter</button>
                        <a href="{{ route('project-task.index') }}" class="px-3 py-2 text-gray-600 hover:underline">Reset</a>
                    </form>
                    <a href="{{ route('project-task.create') }}" class="rounded bg-blue-600 px-4 py-2 font-semibold text-white">+ Tambah Tugas</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Tugas</th>
                                <th class="px-4 py-3 text-left">Proyek</th>
                                <th class="px-4 py-3 text-left">Penanggung jawab</th>
                                <th class="px-4 py-3 text-left">Deadline</th>
                                <th class="px-4 py-3 text-left">Prioritas</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($tasks as $task)
                                <tr>
                                    <td class="px-4 py-3">{{ $task->task_name }}</td>
                                    <td class="px-4 py-3">{{ $task->projectAssignment?->proyek?->nama_proyek ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $task->projectAssignment?->user?->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $task->due_date->format('d-m-Y') }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($task->priority) }}</td>
                                    <td class="px-4 py-3">{{ str_replace('_', ' ', ucfirst($task->status)) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('project-task.show', $task) }}" class="text-blue-700 hover:underline">Detail</a>
                                        <a href="{{ route('project-task.edit', $task) }}" class="ml-2 text-indigo-700 hover:underline">Edit</a>
                                        <form action="{{ route('project-task.destroy', $task) }}" method="POST" class="ml-2 inline"
                                            onsubmit="return confirm('Yakin ingin menghapus tugas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-700 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada tugas proyek.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $tasks->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
